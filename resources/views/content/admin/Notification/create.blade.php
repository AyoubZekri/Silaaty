<div class="modal fade" id="create-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="create-form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إنشاء إشعار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="Title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المحتوى</label>
                        <textarea name="body" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" id="create-submit" class="btn btn-primary">إرسال</button>
                </div>
            </div>
        </form>
    </div>
</div>
