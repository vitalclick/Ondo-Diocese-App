<section class="content">
    <!-- Page content-->
    <div class="container-fluid">
        <div class="block-header">
            <h2>Add New Book</h2>
        </div>


            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

                        <div class="body">
                          <div class="card-inner">
                          <form method="POST" action="<?php echo base_url(); ?>savenewebook" enctype="multipart/form-data" style="margin-top:30px;">

                            <div class="addon-line" style="margin-top:20px;">

                              <div class="form-line">
                                  <label>Ebook Author</label>
                                  <input type="text" class="form-control" name="author" placeholder="Ebook Author">
                              </div>
                            </div>


                            <div class="input-group addon-line" style="margin-top:20px;">

                                <div class="form-line">
                                    <label>Ebook Title</label>
                                    <input type="text" class="form-control" name="title" placeholder="Ebook Title" required="" autofocus="">
                                </div>
                            </div>

                            <div class="input-group addon-line" style="margin-top:20px;">

                                <div class="form-line">
                                    <label>Ebook Description</label>
                                    <textarea type="text" class="form-control" name="description" placeholder="Ebook Description" required="" autofocus=""></textarea>
                                </div>
                            </div>



                            <div class="input-group addon-line" style="margin-top:20px;">

                                <div class="form-line">
                                  <label>Ebook Thumbnail</label>
                                    <input type="file" name="thumbnail" data-allowed-file-extensions="png jpg jpeg PNG" class="thumbs_dropify" required>
                                </div>
                            </div>

                            <div class="input-group addon-line" style="margin-top:20px;">

                                <div class="form-line">
                                    <label>Ebook Amount</label>
                                    <input type="number" min="0" step="any" class="form-control" name="amount" placeholder="Ebook Amount" required="" autofocus="">
                                </div>
                            </div>

                             <?php $this->load->helper('form'); ?>
                             <div class="row">
                                 <div class="col-md-12">
                                     <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                                 </div>
                             </div>
                             <?php
                             $this->load->helper('form');
                             $error = $this->session->flashdata('error');
                             if($error)
                             {
                                 ?>
                                 <div class="alert alert-danger alert-dismissable">
                                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                     <?php echo $error; ?>
                                 </div>
                             <?php }
                             $success = $this->session->flashdata('success');
                             if($success)
                             {
                                 ?>
                                 <div class="alert alert-success alert-dismissable">
                                     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                     <?php echo $success; ?>
                                 </div>
                             <?php } ?>

                            <div class="box-footer text-center">
                               <button class="btn btn-primary waves-effect" type="submit">SAVE NEW EBOOK</button>
                            </div>

                          </form>
                        </div>
                      </div>
                    </div>
                </div>
    </div>
</section>
