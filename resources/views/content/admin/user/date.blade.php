<div class="modal fade" id="makeExperimentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="makeExperimentForm">
                <div class="modal-header">
                    <h5 class="modal-title">تفعيل فترة الاشتراك</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="make_experiment_user_id">
                    <div class="mb-3">
                        <label for="make_expires_at" class="form-label">تاريخ نهاية الاشتراك</label>
                        <input type="date" class="form-control" id="make_expires_at" name="expires_at" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">تفعيل الاشتراك</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="experimentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="experimentForm">
                <div class="modal-header">
                    <h5 class="modal-title">تفعيل الفترة التجريبية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="experiment_user_id">
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">تاريخ نهاية التجربة</label>
                        <input type="date" class="form-control" id="expires_at" name="expires_at" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">تفعيل التجريبي</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
