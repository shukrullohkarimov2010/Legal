<main id="content" role="main" class="main">
    <!-- Content -->
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item"><a class="breadcrumb-link" href="ecommerce-products.html">Products</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Product</li>
                        </ol>
                    </nav>

                    <h1 class="page-header-title">Add Product</h1>

                    <div class="mt-2">
                        <a class="text-body me-3" href="javascript:;">
                            <i class="bi-clipboard me-1"></i> Duplicate
                        </a>
                        <a class="text-body" href="javascript:;">
                            <i class="bi-eye me-1"></i> Preview
                        </a>
                    </div>
                </div>
                <!-- End Col -->
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">@yield('title-content')</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        @yield('content')
                    </div>
                    <!-- Body -->
                </div>
                <!-- End Card -->

                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header card-header-content-between">
                        <h4 class="card-header-title">Media</h4>

                        <!-- Dropdown -->
                        <div class="dropdown">
                            <a class="btn btn-ghost-secondary btn-sm" href="#!" id="mediaDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                Add media from URL <i class="bi-chevron-down"></i>
                            </a>

                            <div class="dropdown-menu dropdown-menu-end mt-1">
                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#addImageFromURLModal">
                                    <i class="bi-link dropdown-item-icon"></i> Add image from URL
                                </a>
                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#embedVideoModal">
                                    <i class="bi-youtube dropdown-item-icon"></i> Embed video
                                </a>
                            </div>
                        </div>
                        <!-- End Dropdown -->
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <!-- Dropzone -->
                        <div id="attachFilesNewProjectLabel" class="js-dropzone dz-dropzone dz-dropzone-card">
                            <div class="dz-message">
                                <img class="avatar avatar-xl avatar-4x3 mb-3" src="{{asset('admin/assets/svg/illustrations/oc-browse.svg')}}" alt="Image Description" data-hs-theme-appearance="default">
                                <img class="avatar avatar-xl avatar-4x3 mb-3" src="{{asset('admin/assets/svg/illustrations-light/oc-browse.svg')}}" alt="Image Description" data-hs-theme-appearance="dark">

                                <h5>Drag and drop your file here</h5>

                                <p class="mb-2">or</p>

                                <span class="btn btn-white btn-sm">Browse files</span>
                            </div>
                        </div>
                        <!-- End Dropzone -->
                    </div>
                    <!-- Body -->
                </div>
                <!-- End Card -->

                <!-- Card -->
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">Variants</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <h6 class="text-cap">Options</h6>

                        <div class="js-add-field" data-hs-add-field-options='{
                    "template": "#addAnotherOptionFieldTemplate",
                    "container": "#addAnotherOptionFieldContainer",
                    "defaultCreated": 0
                  }'>
                            <div class="row mb-4">
                                <div class="col-sm-4 mb-2 mb-sm-0">
                                    <!-- Select -->
                                    <div class="tom-select-custom">
                                        <select class="js-select form-select" data-hs-tom-select-options='{
                                "searchInDropdown": false,
                                "hideSearch": true
                              }'>
                                            <option value="Size">Size</option>
                                            <option value="Color">Color</option>
                                            <option value="Material">Material</option>
                                            <option value="Style">Style</option>
                                            <option value="Title">Title</option>
                                        </select>
                                    </div>
                                    <!-- End Select -->
                                </div>
                                <!-- End Col -->

                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Enter tags" aria-label="Enter tags">
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->

                            <!-- Container For Input Field -->
                            <div id="addAnotherOptionFieldContainer"></div>

                            <a href="javascript:;" class="js-create-field form-link">
                                <i class="bi-plus"></i> Add another option
                            </a>
                        </div>

                        <!-- Add Another Option Input Field -->
                        <div id="addAnotherOptionFieldTemplate" style="display: none;">
                            <div class="row mb-4">
                                <div class="col-sm-4 mb-2 mb-sm-0">
                                    <!-- Select -->
                                    <div class="tom-select-custom">
                                        <select class="js-select-dynamic form-select" data-hs-tom-select-options='{
                                "searchInDropdown": false,
                                "hideSearch": true
                              }'>
                                            <option value="Size">Size</option>
                                            <option value="Color">Color</option>
                                            <option value="Material">Material</option>
                                            <option value="Style">Style</option>
                                            <option value="Title">Title</option>
                                        </select>
                                    </div>
                                    <!-- End Select -->
                                </div>
                                <!-- End Col -->

                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Enter tags" aria-label="Enter tags">
                                </div>
                                <!-- End Col -->
                            </div>
                            <!-- End Row -->
                        </div>
                        <!-- End Add Another Option Input Field -->
                    </div>
                    <!-- Body -->
                </div>
                <!-- End Card -->
            </div>
            <!-- End Col -->


            <!-- End Col -->
        </div>
        <!-- End Row -->

    </div>
    <!-- End Content -->

    @include('admin.partials.footer')
</main>
<!-- ========== END MAIN CONTENT ========== -->
