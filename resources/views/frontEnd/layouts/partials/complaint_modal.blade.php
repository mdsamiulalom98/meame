<div>
    <!-- track area modal -->
    <div class="complaint-modal-backdrop  complaint-close" ></div>
    <div class="complaint-modal-box ">
        <div class="complaint-modal-inner">
            <div class="complaint-modal-header">
                <div class="title">
                    <h3>File a Complaint</h3>
                </div>
                <button class="complaint-close">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="complaint-modal-body">
                <div class="complaint-modal-body-inner">
                    <div class="complaint-image-box">
                        <div id="previewContainer" class="complaint-modal-image"></div>
                    </div>
                    <form id="uploadForm" action="{{ route('complaint.submit') }}" enctype="multipart/form-data" method="POST">
                        @csrf
                        <div class="complaint-modal-file">
                            <input type="file" id="fileInput" name="images[]" multiple />

                            <div class="complaint-modal-icon">
                                <i class="fa fa-camera"></i>
                                <b>Upload a Photo</b>
                                <small>of your complaint</small>
                            </div>
                        </div>

                        <div class="complaint-modal-input">
                            <label for=""><span>*</span>Description (Optional)</label>
                            <textarea placeholder="Ex 01XXXXXXXXX" name="description" ></textarea>
                        </div>

                        <div class="complaint-modal-submit">
                            <button type="button" class="complaint-close" >Close</button>
                            <button type="submit" :disabled="!isFormValid">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
