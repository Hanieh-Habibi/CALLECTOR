<!DOCTYPE html>
<?php
require_once '../config.php';
require_once 'user.data.php';
require_once '../shared-modules/extra_modules.php';
require_once '../lara-content/lara_content.data.php';
require_once '../account/account.class.php';

if(!isset($_SESSION[SessionIndex['UserID']]) || !isset($_REQUEST["id"]))
{
    header("Location:../index.php");
    exit();
}

$UserID = $_REQUEST['id'];

$userWhere = " UserID = :uID";
$userWhereParam = array(":uID" => $UserID);
$res = account::search_account($userWhere, $userWhereParam);
$accountInfo = $res[0];
$UserName = $accountInfo['UserName'];
$UserEmail = $accountInfo['Email'];

$userInfo = user::search_user($userWhere, $userWhereParam);
if(!empty($userInfo))
    $userObj = fill_user($userInfo);
else
    $userObj = make_empty_user($UserID);

$userObj = (array) $userObj ;

$displayName = ($userObj['DisplayName'] != "--") ?
    $userObj['DisplayName'] : $UserName ;

$laraWhere = " IsDeleted = 'NO' and CreatorID = :cID";
$laraWhereParam = array(":cID" => $UserID);
$laraList = search_content_for_feed($laraWhere, $laraWhereParam);
$laraSegmentList = search_content_segment_for_feed($laraWhere, $laraWhereParam);
?>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $UserName; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="" />
	<meta name="keywords" content="" />
	<link rel="stylesheet" type="text/css" href="../css/animate.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../css/flatpickr.min.css">
	<link rel="stylesheet" type="text/css" href="../css/line-awesome.css">
	<link rel="stylesheet" type="text/css" href="../css/line-awesome-font-awesome.min.css">
	<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" type="text/css" href="../css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="../lib/slick/slick.css">
	<link rel="stylesheet" type="text/css" href="../lib/slick/slick-theme.css">
	<link rel="stylesheet" type="text/css" href="../css/style.css">
	<link rel="stylesheet" type="text/css" href="../css/responsive.css">
</head>

<body>
	<div class="wrapper">
        <?php include_once('../header.php'); ?>
        <section class="cover-sec">
            <?php
            if(empty($userObj["CoverPictureExtension"]))
                echo '<img src="../images/resources/cover-img.jpg" height=400 alt="">';
             else
                echo '<img src="' .
                    image_to_base64(Repository . 'user-cover-picture/' . $UserID . '.' . $userObj["CoverPictureExtension"]) .
                    '"  height=400>';
            if($_SESSION[SessionIndex['UserID']] == $UserID)
            {
            ?>
			<div class="add-pic-box">
				<div class="container">
					<div class="row no-gutters">
                        <div class="col-lg-12 col-sm-12">
                            <form method="POST" id="UserCoverPicture" enctype='multipart/form-data'>
                                <input type="file" id="CoverPictureFile" name="CoverPictureFile" onchange="save_picture('#UserCoverPicture')">
                                <label for="CoverPictureFile">Change Image</label>
                                <input type="hidden" id="UserID" name="UserID" value=<?php echo '"' . $UserID . '"'; ?>>
                                <input type="hidden" id="task" name="task" value="save_cover_picture">
                            </form>
                        </div>
					</div>
				</div>
			</div>
            <?php } ?>
        </section>

		<main>
			<div class="main-section">
				<div class="container">
					<div class="main-section-data">
						<div class="row">
							<div class="col-lg-3">
								<div class="main-left-sidebar">
									<div class="user_profile">
										<div class="user-pro-img">
                                            <?php
                                            if(empty($userObj["ProfilePictureExtension"]))
                                                $userProfileImgSrc = "../images/resources/profile-icon-tiny.png";
                                            else
                                                $userProfileImgSrc = image_to_base64(Repository . 'user-profile-picture/' .
                                                    $UserID . '.' . $userObj["ProfilePictureExtension"]);
                                            echo '<img src="' . $userProfileImgSrc . '" width="170" height="170">';
                                            if($_SESSION[SessionIndex['UserID']] == $UserID)
                                            {
                                            ?>
											<div class="add-dp" id="OpenImgUpload">
                                                <form method="POST" id="UserProfilePicture" enctype='multipart/form-data'>
                                                    <input type="file" id="ProfilePictureFile" name="ProfilePictureFile"
                                                                onchange="save_picture('#UserProfilePicture')">
                                                    <label for="ProfilePictureFile"><i class="fas fa-camera"></i></label>
                                                    <input type="hidden" id="UserID" name="UserID" value=<?php echo '"' . $UserID . '"'; ?>>
                                                    <input type="hidden" id="task" name="task" value="save_profile_picture">
                                                </form>
                                            </div>
                                            <?php } ?>
                                        </div><!--user-pro-img end-->
										<div class="user_pro_status">
											<ul class="flw-status">
												<li>
													<span>Following</span>
													<b>---</b>
												</li>
												<li>
													<span>Followers</span>
													<b>---</b>
												</li>
											</ul>
										</div><!--user_pro_status end-->
										<?php if(!empty($userObj["WebsiteAddress"])){?>
                                        <ul class="social_links">
											<li>
                                                <a href=<?php echo $userObj["WebsiteAddress"]; ?> target="_blank">
                                                    <i class="la la-globe"></i> my website
                                                </a>
                                            </li>
										</ul>
                                        <?php } ?>
									</div><!--user_profile end-->
									<!--div class="suggestions full-width">
										<div class="sd-title">
											<h3>People Viewed Profile</h3>
											<i class="la la-ellipsis-v"></i>
										</div>< !--sd-title end-- >
										<div class="suggestions-list">
											<div class="suggestion-usd">
												<img src="../images/resources/s1.png" alt="">
												<div class="sgt-text">
													<h4>Jessica William</h4>
													<span>Graphic Designer</span>
												</div>
												<span><i class="la la-plus"></i></span>
											</div>
											<div class="view-more">
												<a href="../#" title="">View More</a>
											</div>
										</div>< !--suggestions-list end-- >
									</div>< !--suggestions end-->
								</div><!--main-left-sidebar end-->
							</div>

							<div class="col-lg-6">
								<div class="main-ws-sec">
									<div class="user-tab-sec rewivew">
										<h3>
                                            <?php echo $userObj["FirstName"] . " " . $userObj["LastName"]; ?>
                                        </h3>
										<!--div class="star-descp">
											<span>LARA content creator</span>
											<ul>
												<li><i class="fa fa-star"></i></li>
												<li><i class="fa fa-star-half-o"></i></li>
											</ul>
										</div>< !--star-descp end-->
                                        <div class="tab-feed st2 settingjb">
											<ul>
												<li data-tab="feed-dd" class="active">
													<a href="../#" title="">
														<img src="../images/ic1.png" alt="">
														<span>Feed</span>
													</a>
												</li>
												<!--li data-tab="info-dd">
													<a href="../#" title="">
														<img src="../images/ic2.png" alt="">
														<span>Info</span>
													</a>
												</li>
												<li data-tab="saved-jobs">
													<a href="../#" title="">
														<img src="../images/ic4.png" alt="">
														<span>Contents</span>
													</a>
												</li>
												<li data-tab="portfolio-dd">
													<a href="../#" title="">
														<img src="../images/ic3.png" alt="">
														<span>Portfolio</span>
													</a>
												</li>
												<li data-tab="rewivewdata">
													<a href="../#" title="">
														<img src="../images/review.png" alt="">
														<span>Reviews</span>
													</a>
												</li-->
											</ul>
										</div><!-- tab-feed end-->
									</div><!--user-tab-sec end-->

                                    <div class="product-feed-tab current" id="feed-dd">
                                        <div id="audio_container" style="width:0;height:0;overflow:hidden"></div>
                                        <div class="posts-section">
                                            <!-- start of the posts in the feed -->
                                            <?php for($i = 0; $i < count($laraList); $i++)
                                                {
                                                    $laraInfo = $laraList[$i];
                                                    $contentID = $laraInfo['ContentID'];
                                                    $laraSegmentInfo = $laraSegmentList[$contentID];
                                                ?>
                                            <div class="post-bar">
                                                <div class="post_topbar">
                                                    <div class="usy-dt">
                                                        <?php echo '<img src="' . $userProfileImgSrc . '" width="50" height="50">';?>
                                                        <div class="usy-name">
                                                            <h3><?php echo $displayName; ?></h3>
                                                            <span><img src="../images/clock.png" alt="">3 min ago</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="job_descp">
                                                    <h3><?php echo $laraInfo["ContentName"]; ?></h3>
                                                    <ul class="job-dt">
                                                        <li><span>This content is now in the <?php echo $laraInfo["ContentStatus"]; ?> step.</span></li>
                                                    </ul>
                                                    <?php
                                                        for($j=1; $j <= count($laraSegmentInfo); $j++)
                                                        {
                                                            echo "<ul class='segment-info'><li><span>" . $laraSegmentInfo[$j]["SegmentInL2"] . "</span></li>";
                                                            if(!empty($laraSegmentInfo[$j]["RecordingFileName"]))
                                                                echo "<li><img src='../images/ic7.png' 
                                                                            onclick='play_segment_voice(\"" . $laraSegmentInfo[$j]["RecordingFileName"] . "\")'></li>";
                                                            echo "</ul>";
                                                            if(!empty($laraSegmentInfo[$j]["SegmentInL1"]))
                                                            {
                                                                echo "<ul class='segment-info'>
                                                                        <li><img src='../images/ic8.png' onclick='manage_translation(\"" . $contentID . "_" . $j . "\")'></li>
                                                                        <li><span id='" . $contentID . "_" . $j . "' style='display: none'>" . $laraSegmentInfo[$j]["SegmentInL1"] . "</span></li>
                                                                        </ul>";
                                                            }
                                                            echo "<br />";
                                                        }
                                                     ?>
                                                    <br/>
                                                    <?php if(!empty($laraInfo['WebAddress']))
                                                            echo '<a href="' . $laraInfo['WebAddress'] . '" target="_blank">view text</a>';
                                                        else
                                                            echo 'Pages are not available yet.';
                                                    ?>
                                                    <ul class="skill-tags">
                                                        <li><?php echo $laraInfo['l1Name'];?></li>
                                                        <li><?php echo $laraInfo['l2Name'];?></li>
                                                    </ul>
                                                </div>
                                                <!--div class="job-status-bar">
                                                    <ul class="like-com">
                                                        <li>
                                                            <a href="../#"><i class="fas fa-heart"></i> Like</a>
                                                            <img src="../images/liked-img.png" alt="">
                                                            <span>25</span>
                                                        </li>
                                                        <li><a href="../#" class="com"><i class="fas fa-comment-alt"></i> Comment 15</a></li>
                                                    </ul>
                                                    <a href="../#"><i class="fas fa-eye"></i>Views 50</a>
                                                </div-->
                                            </div><!--post-bar end-->
                                            <?php } ?>
                                        </div><!--posts-section end-->
                                    </div><!--product-feed-tab feed-dd end-->
                                    <div class="product-feed-tab" id="info-dd">
                                        <div class="user-profile-ov">
                                            <h3><a href="../#" title="" class="overview-open">Overview</a>
                                                <a href="../#" title="" class="overview-open"><i class="fa fa-pencil"></i></a></h3>
                                            <p>here is overview .</p>
                                        </div><!--user-profile-ov end-->
                                        <div class="user-profile-ov st2">
                                            <h3><a href="../#" title="" class="exp-bx-open">Experience </a><a href="../#" title="" class="exp-bx-open"><i class="fa fa-pencil"></i></a> <a href="../#" title="" class="exp-bx-open"><i class="fa fa-plus-square"></i></a></h3>
                                            <h4>Web designer <a href="../#" title=""><i class="fa fa-pencil"></i></a></h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor aliquam felis, nec condimentum ipsum commodo id. Vivamus sit amet augue nec urna efficitur tincidunt. Vivamus consectetur aliquam lectus commodo viverra. </p>
                                            <h4>UI / UX Designer <a href="../#" title=""><i class="fa fa-pencil"></i></a></h4>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor aliquam felis, nec condimentum ipsum commodo id.</p>
                                            <h4>PHP developer <a href="../#" title=""><i class="fa fa-pencil"></i></a></h4>
                                            <p class="no-margin">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor aliquam felis, nec condimentum ipsum commodo id. Vivamus sit amet augue nec urna efficitur tincidunt. Vivamus consectetur aliquam lectus commodo viverra. </p>
                                        </div><!--user-profile-ov end-->
                                        <div class="user-profile-ov">
                                            <h3><a href="../#" title="" class="ed-box-open">Education</a> <a href="../#" title="" class="ed-box-open"><i class="fa fa-pencil"></i></a> <a href="../#" title=""><i class="fa fa-plus-square"></i></a></h3>
                                            <h4>Master of Computer Science</h4>
                                            <span>2015 - 2018</span>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque tempor aliquam felis, nec condimentum ipsum commodo id. Vivamus sit amet augue nec urna efficitur tincidunt. Vivamus consectetur aliquam lectus commodo viverra. </p>
                                        </div><!--user-profile-ov end-->
                                        <div class="user-profile-ov">
                                            <h3><a href="../#" title="" class="lct-box-open">Location</a> <a href="../#" title="" class="lct-box-open"><i class="fa fa-pencil"></i></a> <a href="../#" title=""><i class="fa fa-plus-square"></i></a></h3>
                                            <h4>India</h4>
                                            <p>151/4 BT Chownk, Delhi </p>
                                        </div><!--user-profile-ov end-->
                                        <div class="user-profile-ov">
                                            <h3><a href="../#" title="" class="skills-open">Skills</a> <a href="../#" title="" class="skills-open"><i class="fa fa-pencil"></i></a> <a href="../#"><i class="fa fa-plus-square"></i></a></h3>
                                            <ul>
                                                <li><a href="../#" title="">HTML</a></li>
                                                <li><a href="../#" title="">PHP</a></li>
                                                <li><a href="../#" title="">CSS</a></li>
                                                <li><a href="../#" title="">Javascript</a></li>
                                                <li><a href="../#" title="">Wordpress</a></li>
                                                <li><a href="../#" title="">Photoshop</a></li>
                                                <li><a href="../#" title="">Illustrator</a></li>
                                                <li><a href="../#" title="">Corel Draw</a></li>
                                            </ul>
                                        </div><!--user-profile-ov end-->
                                    </div><!--product-feed-tab info-dd end-->
                                    <div class="product-feed-tab" id="saved-jobs">
										<ul class="nav nav-tabs" id="myTab" role="tablist">
                                           <li class="nav-item">
                                             <a class="nav-link active" id="mange-tab" data-toggle="tab" href="#mange" role="tab" aria-controls="home" aria-selected="true">Manage Jobs</a>
                                           </li>
                                           <li class="nav-item">
                                             <a class="nav-link" id="saved-tab" data-toggle="tab" href="#saved" role="tab" aria-controls="profile" aria-selected="false">Saved Jobs</a>
                                           </li>
                                           <li class="nav-item">
                                             <a class="nav-link" id="contact-tab" data-toggle="tab" href="#applied" role="tab" aria-controls="applied" aria-selected="false">Applied Jobs</a>
                                           </li>
                                           <li class="nav-item">
                                             <a class="nav-link" id="cadidates-tab" data-toggle="tab" href="#cadidates" role="tab" aria-controls="contact" aria-selected="false">Applied cadidates</a>
                                           </li>
                                         </ul>
                                         <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="mange" role="tabpanel" aria-labelledby="mange-tab">
                                                <div class="posts-bar">
                                                    <div class="post-bar bgclr">
                                                        <div class="wordpressdevlp">
                                                            <h2>Senior Wordpress Developer</h2>
                                                           
                                                            <p><i class="la la-clock-o"></i>Posted on 30 August 2018</p>
                                                        </div>
                                                        <br>
                                                        <div class="row no-gutters">
                                                            <div class="col-md-6 col-sm-12">
                                                                <div class="cadidatesbtn">
                                                                    <button type="button" class="btn btn-primary">
                                                                        <span class="badge badge-light">3</span>Candidates
                                                                    </button>
                                                                    <a href="../#">
																		<i class="far fa-edit"></i>
                                                                    </a>
                                                                    <a href="../#">
                                                                        <i class="far fa-trash-alt"></i>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-12">
                                                                <ul class="bk-links bklink">
                                                                    <li><a href="../#" title=""><i class="la la-bookmark"></i></a></li>
                                                                    <li><a href="../#" title=""><i class="la la-envelope"></i></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="saved" role="tabpanel" aria-labelledby="saved-tab">
                                                <div class="post-bar">
                                                    <div class="p-all saved-post">
                                                        <div class="usy-dt">
                                                            <div class="wordpressdevlp">
                                                                <h2>Senior PHP Developer</h2>
                                                                
                                                                <p><i class="la la-clock-o"></i>Posted on 30 August 2018</p>
                                                            </div>
                                                        </div>
                                                        <div class="ed-opts">
                                                            <a href="../#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
                                                            <ul class="ed-options">
                                                                <li><a href="../#" title="">Edit Post</a></li>
                                                                <li><a href="../#" title="">Unsaved</a></li>
                                                                <li><a href="../#" title="">Unbid</a></li>
                                                                <li><a href="../#" title="">Close</a></li>
                                                                <li><a href="../#" title="">Hide</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <ul class="savedjob-info saved-info">
                                                        <li>
                                                            <h3>Applicants</h3>
                                                            <p>10</p>
                                                        </li>
                                                        <li>
                                                            <h3>Job Type</h3>
                                                            <p>Full Time</p>
                                                        </li>
                                                        <li>
                                                            <h3>Salary</h3>
                                                            <p>$600 - Mannual</p>
                                                        </li>
                                                        <li>
                                                            <h3>Posted : 5 Days Ago</h3>
                                                            <p>Open</p>
                                                        </li>
                                                        <div class="devepbtn saved-btn">
                                                            <a class="clrbtn" href="../#">Unsaved</a>
                                                            <a class="clrbtn" href="../#">Message</a>
                                                        </div>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="applied" role="tabpanel" aria-labelledby="applied-tab">
                                                <div class="post-bar">
                                                    <div class="p-all saved-post">
                                                        <div class="usy-dt">
                                                            <div class="wordpressdevlp">
                                                                <h2>UI UX Designer</h2>
                                                               
                                                                <p><i class="la la-clock-o"></i>Posted on 30 August 2018</p>
                                                            </div>
                                                        </div>
                                                        <div class="ed-opts">
                                                            <a href="../#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
                                                            <ul class="ed-options">
                                                                <li><a href="../#" title="">Edit Post</a></li>
                                                                <li><a href="../#" title="">Unsaved</a></li>
                                                                <li><a href="../#" title="">Unbid</a></li>
                                                                <li><a href="../#" title="">Close</a></li>
                                                                <li><a href="../#" title="">Hide</a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                    <ul class="savedjob-info saved-info">
                                                        <li>
                                                            <h3>Applicants</h3>
                                                            <p>10</p>
                                                        </li>
                                                        <li>
                                                            <h3>Job Type</h3>
                                                            <p>Full Time</p>
                                                        </li>
                                                        <li>
                                                            <h3>Salary</h3>
                                                            <p>$600 - Mannual</p>
                                                        </li>
                                                        <li>
                                                            <h3>Posted : 5 Days Ago</h3>
                                                            <p>Open</p>
                                                        </li>
                                                        <div class="devepbtn saved-btn">
                                                            <a class="clrbtn" href="../#">Applied</a>
                                                            <a class="clrbtn" href="../#">Message</a>
                                                            <a href="../#">
                                                                <i class="far fa-trash-alt"></i>
                                                            </a>
                                                        </div>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="cadidates" role="tabpanel" aria-labelledby="cadidates-tab">
                                                <div class="post-bar">
                                                    <div class="post_topbar applied-post">
                                                        <div class="usy-dt">
                                                            <img src="../images/resources/us-pic.png" alt="">
                                                            <div class="usy-name">
                                                                <h3>John Doe</h3>
                                                                <div class="epi-sec epi2">
                                                                    <ul class="descp descptab bklink">
                                                                        <li><img src="../images/icon8.png" alt=""><span>Epic Coder</span></li>
                                                                        <li><img src="../images/icon9.png" alt=""><span>India</span></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="ed-opts">
                                                            <a href="../#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
                                                            <ul class="ed-options">
                                                                <li><a href="../#" title="">Edit Post</a></li>
                                                                <li><a href="../#" title="">Accept</a></li>
                                                                <li><a href="../#" title="">Unbid</a></li>
                                                                <li><a href="../#" title="">Close</a></li>
                                                                <li><a href="../#" title="">Hide</a></li>
                                                            </ul>
                                                        </div>
                                                        <div class="job_descp noborder">
                                                            <div class="star-descp review profilecnd">
                                                                <ul class="bklik">
                                                                    <li><i class="fa fa-star"></i></li>
                                                                    <li><i class="fa fa-star"></i></li>
                                                                    <li><i class="fa fa-star"></i></li>
                                                                    <li><i class="fa fa-star"></i></li>
                                                                    <li><i class="fa fa-star-half-o"></i></li>
                                                                    <a href="../#" title="">5.0 of 5 Reviews</a>
                                                                </ul>
                                                            </div>
                                                            <div class="devepbtn appliedinfo noreply">
                                                                <a class="clrbtn" href="../#">Accept</a>
                                                                <a class="clrbtn" href="../#">View Profile</a>
                                                                <a class="clrbtn" href="../#">Message</a>
                                                                <a href="../#">
                                                                    <i class="far fa-trash-alt"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>	
                                         </div>
									</div><!--product-feed-tab saved-jobs end-->
                                    <div class="product-feed-tab" id="portfolio-dd">
                                        <div class="portfolio-gallery-sec">
                                            <h3>Portfolio</h3>
                                            <div class="portfolio-btn">
                                                <a href="../#" title=""><i class="fas fa-plus-square"></i> Add Portfolio</a>
                                            </div>
                                            <div class="gallery_pf">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6">
                                                        <div class="gallery_pt">
                                                            <img src="../images/resources/pf-img1.jpg" alt="">
                                                            <a href="../#" title=""><img src="../images/all-out.png" alt=""></a>
                                                        </div><!--gallery_pt end-->
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6">
                                                        <div class="gallery_pt">
                                                            <img src="../images/resources/pf-img2.jpg" alt="">
                                                            <a href="../#" title=""><img src="../images/all-out.png" alt=""></a>
                                                        </div><!--gallery_pt end-->
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6">
                                                        <div class="gallery_pt">
                                                            <img src="../images/resources/pf-img3.jpg" alt="">
                                                            <a href="../#" title=""><img src="../images/all-out.png" alt=""></a>
                                                        </div><!--gallery_pt end-->
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-6 col-6">
                                                        <div class="gallery_pt">
                                                            <img src="../images/resources/pf-img4.jpg" alt="">
                                                            <a href="../#" title=""><img src="../images/all-out.png" alt=""></a>
                                                        </div><!--gallery_pt end-->
                                                    </div>
                                                </div>
                                            </div><!--gallery_pf end-->
                                        </div><!--portfolio-gallery-sec end-->
                                    </div><!--product-feed-tab portfolio-dd end-->
                                    <div class="product-feed-tab" id="rewivewdata">
                                        <section ></section>
										<div class="posts-section">
											<div class="post-bar reviewtitle">
												<h2>Reviews</h2>
											</div><!--post-bar end-->
											<div class="post-bar ">
												<div class="post_topbar">
													<div class="usy-dt">
														<img src="../images/resources/bg-img3.png" alt="">
														<div class="usy-name">
															<h3>Rock William</h3>
															<div class="epi-sec epi2">
                                                                <ul class="descp review-lt">
                                                                    <li><img src="../images/icon8.png" alt=""><span>Epic Coder</span></li>
                                                                    <li><img src="../images/icon9.png" alt=""><span>India</span></li>
                                                                </ul>
                                                            </div>
														</div>
													</div>
												</div>
												<div class="job_descp mngdetl">
                                                    <div class="star-descp review">
                                                            <ul>
                                                                <li><i class="fa fa-star"></i></li>
                                                               <li><i class="fa fa-star-half-o"></i></li>
                                                            </ul>
                                                            <a href="../#" title="">5.0 of 5 Reviews</a>
                                                        </div>
                                                    <div class="reviewtext">
                                                            <p>Lorem ipsum dolor sit amet, adipiscing elit. Nulla luctus mi et porttitor ultrices</p>
                                                            <hr>
                                                        </div>
                                                    <div class="post_topbar post-reply">
                                                            <div class="usy-dt">
                                                                <img src="../images/resources/bg-img4.png" alt="">
                                                                <div class="usy-name">
                                                                    <h3>John Doe</h3>
                                                                    <div class="epi-sec epi2">
                                                                       <p><i class="la la-clock-o"></i>3 min ago</p>
                                                                       <p class="tahnks">Thanks :)</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <div class="post_topbar rep-post rep-thanks">
                                                        <hr>
                                                        <div class="usy-dt">
                                                            <img src="../images/resources/bg-img4.png" alt="">
                                                            <input class="reply" type="text" placeholder="Reply">
                                                            <a class="replybtn" href="../#">Send</a>
                                                        </div>
                                                    </div>
										        </div>
											</div><!--post-bar end-->
											<div class="post-bar post-thanks">
												<div class="post_topbar">
													<div class="usy-dt">
														<img src="../images/resources/bg-img1.png" alt="">
														<div class="usy-name">
															<h3>Jassica William</h3>
															<div class="epi-sec epi2">
													<ul class="descp review-lt">
														<li><img src="../images/icon8.png" alt=""><span>Epic Coder</span></li>
														<li><img src="../images/icon9.png" alt=""><span>India</span></li>
													</ul>
												</div>
														</div>
													</div>
													<div class="ed-opts">
														<a href="../#" title="" class="ed-opts-open"><i class="la la-ellipsis-v"></i></a>
														<ul class="ed-options">
															<li><a href="../#" title="">Edit Post</a></li>
															<li><a href="../#" title="">Unsaved</a></li>
															<li><a href="../#" title="">Unbid</a></li>
															<li><a href="../#" title="">Close</a></li>
															<li><a href="../#" title="">Hide</a></li>
														</ul>
													</div>
												</div>
												<div class="job_descp mngdetl">
												    <div class="star-descp review">
                                                    <ul>
                                                        <li><i class="fa fa-star"></i></li>
                                                        <li><i class="fa fa-star-half-o"></i></li>
                                                    </ul>
                                                    <a href="../#" title="">5.0 of 5 Reviews</a><br><br>
                                                    <p>Awesome Work, Thanks John!</p>
                                                    <hr>
                                                </div>
										            <div class="post_topbar rep-post">
													<div class="usy-dt">
														<img src="../images/resources/bg-img4.png" alt="">
															<input class="reply" type="text" placeholder="Reply">
															<a class="replybtn" href="../#">Send</a>
												    </div>
												</div>
												</div>
											</div><!--post-bar end-->
										</div><!--posts-section end-->
									</div><!--product-feed-tab rewivewdata end-->
								</div><!--main-ws-sec end-->
							</div>

							<div class="col-lg-3">
								<div class="right-sidebar">
									<?php if($_SESSION[SessionIndex['UserID']] == $UserID) { ?>
                                    <div class="message-btn">
										<a href="user-setting.php" title=""><i class="fas fa-cog"></i> Setting</a>
									</div>
                                    <?php } ?>
									<div class="widget widget-portfolio">
										<!--div class="wd-heady">
											<h3>Portfolio</h3>
											<img src="../images/photo-icon.png" alt="">
										</div>
										<div class="pf-gallery">
											<ul>
												<li><a href="../#" title=""><img src="../images/resources/pf-gallery1.png" alt=""></a></li>
												<li><a href="../#" title=""><img src="../images/resources/pf-gallery2.png" alt=""></a></li>
												<li><a href="../#" title=""><img src="../images/resources/pf-gallery3.png" alt=""></a></li>
												<li><a href="../#" title=""><img src="../images/resources/pf-gallery4.png" alt=""></a></li>
											</ul>
										</div>< !--pf-gallery end-->
									</div><!--widget-portfolio end-->
								</div><!--right-sidebar end-->
							</div>
						</div>
					</div><!-- main-section-data end-->
				</div> 
			</div>
		</main>

        <?php include_once('../footer.php'); ?>


        <div class="overview-box" id="overview-box">
			<div class="overview-edit">
				<h3>Overview</h3>
				<span>5000 character left</span>
				<form>
					<textarea></textarea>
					<button type="submit" class="save">Save</button>
					<button type="submit" class="cancel">Cancel</button>
				</form>
				<a href="../#" title="" class="close-box"><i class="la la-close"></i></a>
			</div><!--overview-edit end-->
		</div><!--overview-box end-->
		<div class="overview-box" id="experience-box">
			<div class="overview-edit">
				<h3>Experience</h3>
				<form>
					<input type="text" name="subject" placeholder="Subject">
					<textarea></textarea>
					<button type="submit" class="save">Save</button>
					<button type="submit" class="save-add">Save & Add More</button>
					<button type="submit" class="cancel">Cancel</button>
				</form>
				<a href="../#" title="" class="close-box"><i class="la la-close"></i></a>
			</div><!--overview-edit end-->
		</div><!--overview-box end-->
		<div class="overview-box" id="education-box">
			<div class="overview-edit">
				<h3>Education</h3>
				<form>
					<input type="text" name="school" placeholder="School / University">
					<div class="datepicky">
						<div class="row">
							<div class="col-lg-6 no-left-pd">
								<div class="datefm">
									<input type="text" name="from" placeholder="From" class="datepicker">	
									<i class="fa fa-calendar"></i>
								</div>
							</div>
							<div class="col-lg-6 no-righ-pd">
								<div class="datefm">
									<input type="text" name="to" placeholder="To" class="datepicker">
									<i class="fa fa-calendar"></i>
								</div>
							</div>
						</div>
					</div>
					<input type="text" name="degree" placeholder="Degree">
					<textarea placeholder="Description"></textarea>
					<button type="submit" class="save">Save</button>
					<button type="submit" class="save-add">Save & Add More</button>
					<button type="submit" class="cancel">Cancel</button>
				</form>
				<a href="../#" title="" class="close-box"><i class="la la-close"></i></a>
			</div><!--overview-edit end-->
		</div><!--overview-box end-->
		<div class="overview-box" id="location-box">
			<div class="overview-edit">
				<h3>Location</h3>
				<form>
					<div class="datefm">
						<select>
							<option>Country</option>
							<option value="pakistan">Pakistan</option>
							<option value="england">England</option>
							<option value="india">India</option>
							<option value="usa">United Sates</option>
						</select>
						<i class="fa fa-globe"></i>
					</div>
					<div class="datefm">
						<select>
							<option>City</option>
							<option value="london">London</option>
							<option value="new-york">New York</option>
							<option value="sydney">Sydney</option>
							<option value="chicago">Chicago</option>
						</select>
						<i class="fa fa-map-marker"></i>
					</div>
					<button type="submit" class="save">Save</button>
					<button type="submit" class="cancel">Cancel</button>
				</form>
				<a href="../#" title="" class="close-box"><i class="la la-close"></i></a>
			</div><!--overview-edit end-->
		</div><!--overview-box end-->
		<div class="overview-box" id="skills-box">
			<div class="overview-edit">
				<h3>Skills</h3>
				<ul>
					<li><a href="../#" title="" class="skl-name">HTML</a><a href="../#" title="" class="close-skl"><i class="la la-close"></i></a></li>
					<li><a href="../#" title="" class="skl-name">php</a><a href="../#" title="" class="close-skl"><i class="la la-close"></i></a></li>
					<li><a href="../#" title="" class="skl-name">css</a><a href="../#" title="" class="close-skl"><i class="la la-close"></i></a></li>
				</ul>
				<form>
					<input type="text" name="skills" placeholder="Skills">
					<button type="submit" class="save">Save</button>
					<button type="submit" class="save-add">Save & Add More</button>
					<button type="submit" class="cancel">Cancel</button>
				</form>
				<a href="../#" title="" class="close-box"><i class="la la-close"></i></a>
			</div><!--overview-edit end-->
		</div><!--overview-box end-->
		<div class="overview-box" id="create-portfolio">
			<div class="overview-edit">
				<h3>Create Portfolio</h3>
				<form>
					<input type="text" name="pf-name" placeholder="Portfolio Name">
					<div class="file-submit">
						<input type="file" id="file">
						<label for="file">Choose File</label>	
					</div>
					<div class="pf-img">
						<img src="../images/resources/np.png" alt="">
					</div>
					<input type="text" name="website-url" placeholder="htp://www.example.com">
					<button type="submit" class="save">Save</button>
					<button type="submit" class="cancel">Cancel</button>
				</form>
				<a href="../#" title="" class="close-box"><i class="la la-close"></i></a>
			</div><!--overview-edit end-->
		</div><!--overview-box end-->

	</div><!--theme-layout end-->



<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/popper.js"></script>
<script type="text/javascript" src="../js/bootstrap.min.js"></script>
<script type="text/javascript" src="../js/flatpickr.min.js"></script>
<script type="text/javascript" src="../lib/slick/slick.min.js"></script>
<script type="text/javascript" src="../js/script.js"></script>
<script type="text/javascript" src="user.js"></script>
<script type="text/javascript" src="../lara-content/content.js"></script>
</body>
</html>