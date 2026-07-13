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

<div class="modal fade" id="activationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="activationForm">
                <div class="modal-header">
                    <h5 class="modal-title">تفعيل فترة (مشرف وبائع)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="activation_user_id">
                    <div class="mb-3">
                        <label for="activation_expires_at" class="form-label">تاريخ نهاية التفعيل</label>
                        <input type="date" class="form-control" id="activation_expires_at" name="expires_at" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">تفعيل</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editSellSettingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSellSettingsForm">
                <div class="modal-header">
                    <h5 class="modal-title">إعدادات البيع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="sell_settings_user_id">
                    <div class="mb-3">
                        <label for="sell_type" class="form-label">نوع البيع</label>
                        <select class="form-select" id="sell_type" name="sell_type" required>
                            <option value="1">تجزئة</option>
                            <option value="2">تجزئة ونصف جملة</option>
                            <option value="3">جملة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="max_sellers" class="form-label">عدد البائعين</label>
                        <input type="number" class="form-control" id="max_sellers" name="max_sellers" value="2" required min="0">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">حفظ الإعدادات</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
