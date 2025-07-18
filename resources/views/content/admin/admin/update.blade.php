<div class="modal fade" id="update-modal" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="fw-bold py-1 mb-1">{{ __('Update user') }}</h4>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form class="form-horizontal" onsubmit="event.preventDefault()" action="#"
                  enctype="multipart/form-data" id="update-form">

                  <input type="hidden" class="form-control" id="id" name="id">

                  <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name" autofocus>
                  </div>
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email">
                  </div>
                <div class="mb-3 form-password-toggle">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-group input-group-merge">
                        <input type="password" class="form-control" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" />
                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                    </div>
                  </div>

                  <div class="mb-3" style="text-align: center">
                      <button type="submit" id="update-submit" name="submit"
                          class="btn btn-primary">{{ __('Send') }}</button>
                  </div>

              </form>
          </div>
      </div>
  </div>
</div>
