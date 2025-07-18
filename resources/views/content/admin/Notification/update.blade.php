<div class="modal fade" id="update-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="update-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل إشعار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="update-id" name="id">
                    <div class="mb-3">
                        <label for="update-title" class="form-label">العنوان</label>
                        <input type="text" class="form-control" name="title" id="update-title" required>
                    </div>
                    <div class="mb-3">
                        <label for="update-content" class="form-label">المحتوى</label>
                        <textarea class="form-control" name="content" id="update-content" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="update-submit">حفظ التغييرات</button>
                </div>
            </div>
        </form>
    </div>
</div>
