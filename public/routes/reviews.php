<?php
// Review(s)
$app->group('/review', $authenticate, function () use ($app,$authenticate){
    $app->get('/', $authenticate, function () use ($app){
        $p = $app->request()->get('p');
        $l = $app->request()->get('l');
        $reviews = new \TPS\reviews();
        $albums = $reviews->getAvailableReviews($p,$l);
        $params=array(
            'albums'=>$albums,
            'title'=>'Available Reviews'
            );
        $app->render('reviewList.twig',$params);
    });
    $app->get('/complete' ,$authenticate , function () use ($app){

        $reviews = new \TPS\reviews();
        $review = $reviews->getPendingReviews();
        $params = array(
            "area" => "Reviews",
            "title" => "Pending Approval",
            "reviews" => $review,
        );
        $app->render('reviewListCompleted.twig',$params);
    });
    $app->put('/:id', $authenticate, function ($id) use ($app){ // Update
        $reviews = new \TPS\reviews();
        if($_SESSION['access']<2){
            $app->response->setStatus(401);
            $app->render('error.html.twig',array("status"=>401,"title"=>"Error 403","details"=>array("permission denied")));
        }
        else{
            #$app->render('notSupported.twig');
            $description = $app->request()->post('description');
            $notes = $app->request()->post('notes');
            $reviewer = $app->request()->post('reviewer');
            $hometown = $app->request()->post('hometown');
            $subgenres = $app->request()->post('subgenres');
            $recommend = $app->request()->post('recommend');
            $femcon = $app->request()->post('femcon');
            $approved = $app->request()->post('accepted');
            $id_post = $app->request()->post('id');
            if($id_post != $id){
                $app->halt(400);
            }
            if(!is_numeric($approved)){
                $approved = NULL;
            }
            global $mysqli;
            $update = "UPDATE review SET approved=?, femcon=?, reviewer=?,"
                    . "hometown=?, subgenre=?, description=?, recommendations=?,"
                    . "notes=? where id=?";
            $albums = array();
            $params = array();
            if($stmt = $mysqli->prepare($update)){
                $stmt->bind_param('iissssssi',
                        $approved,$femcon,$reviewer,$hometown,$subgenres,
                        $description,$recommend,$notes,$id);
                if($stmt->execute()){
                    $stmt->close();
                    $app->flash('success',"$id updated succesfully");
                    if($approved){
                        $reviews->setPrintLabel($id);
                    }
                    else{
                        $reviews->clearPrintLabel($id);
                    }
                    $app->redirect('./complete');
                }
                $stmt->close();
            }
            else{
                error_log($mysqli->error);
                $app->halt(500);
            }
            $reviews->setPrintLabel($id);
            $app->response->setStatus(202);
        }

    });
    $app->post('/:id',$authenticate, function ($id) use ($app){ // Create (not allowed)
        $app->render('notSupported.twig');
    });
    $app->get('/:id', $authenticate, function ($id) use ($app){ // Query
        // Create new Album Review
        $reviews = new \TPS\reviews();
        $review = $reviews->getAlbumAndReview($id);
        $review["title"] = "Edit";
        $review["area"] = "Reviews";
        $app->render('review.twig',$review);
    });
    
    $app->group('/print' ,$authenticate , function () use ($app,$authenticate){
        $app->get('/',$authenticate, function () use ($app){
            $p = $app->request()->get('p') ?: 1;
            $max = $app->request()->get('l') ?: 100;
            $review = new \TPS\reviews();
            $numReviews = $review->countApprovedReviews();
            $reviews = $review->getApprovedReviews($p,$max);
            $labels = $review->getPrintLables();
            $pagination = array(
                'currentPage'=>$p,
                'limit'=>$max,
                'max'=>$numReviews,
                    );
            $params = array(
                "area" => "Reviews",
                "title" => "Approved",
                "reviews" => $reviews,
                "pagination" => $pagination,
                "labels" => $labels,
            );
            $app->render('reviewPrint.twig',$params);
        });
        $app->put('/:RefCode', $authenticate, function($RefCode) use ($app){
            $reviews = new \TPS\reviews();
            $result = $reviews->setPrintLabel($RefCode);
            $app->response->setStatus($result[1]);
            print $result[1]." ($RefCode)";
        });
        $app->delete('/:RefCode', $authenticate, function($RefCode) use ($app){
            $reviews = new \TPS\reviews();
            $result = $reviews->clearPrintLabel($RefCode);
            $app->response->setStatus($result[1]);
            print $result[1]." ($RefCode)";
        });
    });

    // review/album group
    $app->group('/album', $authenticate, function () use ($app,$authenticate){
    $app->post('/:refcode', $authenticate, function ($RefCode) use ($app){
        $reviews = new \TPS\reviews();
        $reviews->createReview($app, $RefCode);
        $app->redirect('/review');
    });
    $app->get('/:refcode/new', $authenticate, function ($term) use ($app){
        // Create new Album Review
        $reviews = new \TPS\reviews();
        $library = new \TPS\library();
        $params = $library->GetFullAlbum($term);
        $params['title']="New Review for album #$term";
        $params['area']="Reviews";
        $app->render('review.twig',$params);
    });
    $app->get('/:refcode', $authenticate, function ($term) use ($app){
        $params = array(
            "title" => "Completed Reviews for $term",
        );
        $reviews = new \TPS\reviews();
        $reviews = $reviews->getReviewsByAlbum($term);
        $params['reviews']=$reviews;
        $app->render('reviewListCompleted.twig',$params);
    });
}); // end review/album group
    
    // SEARCH REVIEWS
    $app->group('/search', $authenticate, function () use ($app,$authenticate){
        $app->post('/album', $authenticate, function () use ($app){
            $term = urlencode($app->request()->post('q'));
            $app->redirect("/review/search/album/$term");
        });
        $app->get('/album/', $authenticate, function () use ($app){
            $term = NULL;
            $orig_term = $term;
            $review = new \TPS\reviews();
            $albums = $review->getAlbumReviews($term);
            $params=array(
                'albums'=>$albums,
                'search'=>$orig_term,
                'area'=>'Search',
                'title'=>'Available Reviews'
                );
            $app->render('reviewList.twig',$params);
        });
        $app->get('/album/:term', $authenticate, function ($term) use ($app){
            $review = new \TPS\reviews();
            $albums = $review->getAlbumReviews($term);
            $params=array(
                'albums'=>$albums,
                'search'=>$term,
                'area'=>'Search',
                'title'=>'Available Reviews'
                );
            $app->render('reviewList.twig',$params);
        });
        $app->get('/:term', $authenticate, function ($term) use ($app){
            $reviews = new \TPS\reviews();
            $reviewList = $reviews->getReview($term);
            $params=array(
                'search'=>$term,
                'area'=>'Search',
                'title'=>'Reviews',
                );
            $isXHR = $app->request->isAjax();
            if($isXHR){
                print json_encode($reviewList);
            }
            else{
                $app->render('reviewList.twig',$params);
            }
        });
        $app->get('/', $authenticate, function () use ($app){
            $params=array(
                'area'=>'Reviews',
                'title'=>'Search'
                );
            $app->render('reviewList.twig',$params);
        });
    });
});
