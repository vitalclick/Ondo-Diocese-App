<section class="content">
    <div class="container-fluid">
        <div class="block-header">
            <h2>Book Purchases</h2>
        </div>
    <!-- Page content-->
    <div class="content-wrapper">
        <div class="container-fluid">

            <!-- Exportable Table -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">

                        <div class="body">
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
                          <div style="overflow-x:auto;">
                            <table id="purchases_table" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                  <th>Id</th>
                                  <th>Title</th>
                                  <th>Email</th>
                                  <th>Name</th>
                                  <th>Reference</th>
                                  <th>Shipping</th>
                                  <th>Amount</th>
                                  <th>Date</th>
                                </tr>
                                </thead>

                            </table>
                          </div>
                        </div>
                    </div>
                </div>
    </div>
</section>
