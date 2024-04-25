<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Chapter List - e School</title>

    <!-- css  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/responsive.css" />

</head>

<body>

    <div class="dashboard">

        @include('layouts/header-sidebar')

        <!-- MAIN -->
        <div class="dashboard-app">

            @include('layouts/header-topbar')

            <!-- Sub MAIN -->
            <div class="link-dir">
                <h1 class="display-4">Create Chapter List</h1>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)">Base Table</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="chapter-list.php">Chapter</a></li>
                    <li><a href="javascript:void(0)">/</a></li>
                    <li><a href="javascript:void(0)" class="active-link-dir">Create Chapter List</a></li>
                </ul>
            </div>

            <div class="dashboard-content side-content">

                <form class="s-chapter-form" action="#">

                    <div class="institute-list">

                        <h3>Select Standard</h3>
                        <div class="form-group">
                            <select class="form-control" id="exampleFormControlSelect1">
                                <option>Nursery</option>
                                <option>KG</option>
                                <option>LKG</option>
                                <option>UKG</option>
                            </select>
                        </div>
                        <h3>Subject</h3>
                        <div class="form-group">
                            <select class="form-control" id="exampleFormControlSelect2" placeholder="Subject Name">
                                <option>Mathematics </option>
                            </select>
                        </div>
                        <h3>Chapter Number</h3>
                        <div class="search-box-2 form-group">
                            <input type="search" name="search" placeholder="Chapter Number" class="form-control">
                        </div>
                        <h3>Chapter Name</h3>
                        <div class="search-box-2 form-group">
                            <input type="search" name="search" placeholder="Chapter Name" class="form-control">
                        </div>
                        <h3>Chapter Image</h3>
                        <!-- <div class="input-group">
              <input type="text" class="form-control" placeholder="Search this blog">
              <div class="input-group-append">
                <button class="btn btn-secondary" type="button">
                  <i class="fa fa-search"></i>
                </button>
              </div>
            </div> -->

                        <div class="file">
                            <div class="input-group search-box-2">
                                <input type="text" class="form-control" placeholder="Chapter Image">
                                <div class="input-group-append">
                                    <span class="btn_upload">
                                        <input type="file" id="imag" title="" class="input-img  file__input--label" for="customFile" data-text-btn="Upload" />
                                        Choose Image
                                    </span>
                                </div>
                            </div>
                            <img id="ImgPreview" src="" class="preview1 ImgPreview" />
                            <i class="fas fa-times ml-3 btn-rmv1" id="removeImage1"></i>
                        </div>

                        <div class="add-chapter-btn">
                            <a class="btn" id="addmore">
                                <i class="fas fa-plus"></i>
                            </a>
                            <label for="exampleInputEmail1">Add More Chapter</label>
                        </div>
                    </div>

                    <div class="submit-btn">
                        <input type="submit" value="Submit" class="btn bg-primary-btn text-white mt-4">
                    </div>

                </form>
            </div><!-- Sub Main Col END -->
        </div><!-- MAIN row END -->
        @include('layouts/footer_new')

    </div>

    <!-- js -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
    <script>
        $(document).ready(function() {
            $('#addmore').click(function() {
                addChapter();
            });

            function addChapter() {
                var chapterHtml = `
            <div class="row mt-3 added-chapter">
              <div class="col-lg-12">
               <i class="fas fa-times btn-rmv2 ml-3 remove-chapter"></i>
                  <h3>Chapter Number</h3>
                  <div class="search-box-2 form-group">
                    <input type="search" name="search" placeholder="Chapter Number" class="form-control">
                  </div>
                  <h3>Chapter Name</h3>
                  <div class="search-box-2 form-group">
                    <input type="search" name="search" placeholder="Chapter Name" class="form-control">
                  </div>
                  <h3>Chapter Image</h3>
                  <div class="file">
                    <div class="input-group search-box-2">
                      <input type="text" class="form-control" placeholder="Chapter Image">
                      <div class="input-group-append">
                        <span class="btn_upload">
                          <input type="file" id="imag" title="" class="input-img  file__input--label" for="customFile"
                            data-text-btn="Upload" />
                          Choose Image
                        </span>
                      </div>
                    </div>
                    <img id="ImgPreview" src="" class="preview1 ImgPreview" />
                    <i class="fas fa-times ml-3 btn-rmv1" id="removeImage1"></i>
                  </div>
              </div>
            </div>`;
                $('.add-chapter-btn').before(chapterHtml);
                $('.remove-chapter').click(function() {
                    $(this).closest('.added-chapter').remove();
                });

            }
        });
    </script>

</body>

</html>