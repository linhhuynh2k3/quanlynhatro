<div class="alert alert-danger">
    
    <strong>Cảnh báo:</strong> Khi tài khoản của bạn bị xóa, tất cả dữ liệu và tài nguyên sẽ bị xóa vĩnh viễn. Trước khi xóa tài khoản, vui lòng tải xuống bất kỳ dữ liệu hoặc thông tin nào bạn muốn giữ lại.
</div>

<button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
     Xóa tài khoản
</button>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                     Xác nhận xóa tài khoản
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>Bạn có chắc chắn muốn xóa tài khoản của mình?</strong>
                    </p>
                    <p class="text-muted">
                        Khi tài khoản của bạn bị xóa, tất cả dữ liệu và tài nguyên sẽ bị xóa vĩnh viễn. Vui lòng nhập mật khẩu của bạn để xác nhận bạn muốn xóa vĩnh viễn tài khoản của mình.
                    </p>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">
                             Mật khẩu
                        </label>
                        <input id="password" 
                               name="password" 
                               type="password" 
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                               placeholder="Nhập mật khẩu để xác nhận"
                               required>
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">
                                 {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                         Hủy
                    </button>
                    <button type="submit" class="btn btn-danger">
                         Xóa tài khoản
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
