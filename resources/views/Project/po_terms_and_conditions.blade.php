<!-- Page Wrapper -->
@extends('layouts.layout')
@section('pageTitle')
    PO Terms & Conditions
@endsection
@section('content')
    <div class="page-wrapper">
        <div class="content container-fluid">
            <div class="page-header">
                <div class="row">
                    <div class="col">
                        <h3 class="page-title">Setup</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active">PO Terms & Conditions</li>
                        </ul>
                    </div>
                </div>
            </div>

            @include('_partialView.nofication')
            <div id="tinymceWarning" class="alert alert-warning d-none">
                Rich text editor failed to load. You can still type/paste <strong>HTML</strong> into the Body field, but
                formatting toolbar will be unavailable. (Check internet access or blocked CDN scripts.)
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Add PO Term</h4>
                        </div>
                        <div class="card-body">
                            <form method="post" id="addTermForm">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Title <span class="text-danger">*</span></label>
                                            <?php if (($title ?? '') == '') {
                                                $title = old('title');
                                            } ?>
                                            <input type="text" class="form-control" name="title" required
                                                value="{{ $title }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ordering Rank</label>
                                            <?php if (($ordering_rank ?? '') == '') {
                                                $ordering_rank = old('ordering_rank');
                                            } ?>
                                            <input type="number" min="1" class="form-control" name="ordering_rank"
                                                value="{{ $ordering_rank }}">
                                            <small class="form-text text-muted">Optional. Used to order terms in the PO.
                                                Lower comes first.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Body (HTML)</label>
                                            <?php if (($body ?? '') == '') {
                                                $body = old('body');
                                            } ?>
                                            <textarea class="form-control" name="body" id="add_body" rows="10">{{ $body }}</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary" name="addnew">Add Term</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card card-table">
                        <div class="card-header">
                            <h4 class="card-title">PO Terms & Conditions</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0" id="termsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 80px;">S/N</th>
                                            <th style="width: 140px;">Rank</th>
                                            <th style="width: 260px;">Title</th>
                                            <th>Body Preview</th>
                                            <th style="width: 120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1; @endphp
                                        @if (($terms ?? collect())->count() > 0)
                                            @foreach ($terms as $term)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td>{{ $term->ordering_rank ?? '-' }}</td>
                                                    <td><strong>{{ $term->title }}</strong></td>
                                                    <td>
                                                        {{ \Illuminate\Support\Str::limit(strip_tags($term->body ?? ''), 140) }}
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-sm bg-success-light"
                                                            href="javascript: editfunc('{{ $term->id }}')">
                                                            <i class="fe fe-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-sm bg-danger-light"
                                                            href="javascript: deletefunc('{{ $term->id }}')">
                                                            <i class="fe fe-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center">No terms found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal fade" id="edit_modal" aria-hidden="true" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" id="editTermForm">
                            {{ csrf_field() }}
                            <div class="modal-header">
                                <h5 class="modal-title">Edit PO Term</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Title <span class="text-danger">*</span></label>
                                            <input type="text" id="edit_title" name="title" class="form-control"
                                                required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Ordering Rank</label>
                                            <input type="number" min="1" id="edit_ordering_rank"
                                                name="ordering_rank" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Body (HTML)</label>
                                            <textarea class="form-control" name="body" id="edit_body" rows="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="edit_id" name="id">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" name="update">Save Changes</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Edit Modal -->

            <!-- Delete Modal -->
            <div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form method="post" id="deleteForm">
                            {{ csrf_field() }}
                            <div class="modal-body">
                                <div class="form-content p-2">
                                    <h4 class="modal-title">Delete</h4>
                                    <p class="mb-4">Are you sure want to delete this term?</p>
                                    <button type="submit" class="btn btn-primary" name="del">Continue</button>
                                    <input type="hidden" id="deleteid" name="deleteid">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Delete Modal -->
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
@endsection

@push('head')
    <!-- Place the first <script>
        tag in your HTML 's <head> --> <
        script src =
            "https://cdn.tiny.cloud/1/vq9kexxus5depc0nnx14msjoa8at5tv801goona9kes3jppb/tinymce/8/tinymce.min.js"
        referrerpolicy = "origin"
        crossorigin = "anonymous" >
    </script>
@endpush

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

            <script>
                @php
                    $termsEditData = ($terms ?? collect())
                        ->mapWithKeys(function ($term) {
                            return [
                                $term->id => [
                                    'id' => $term->id,
                                    'title' => $term->title,
                                    'ordering_rank' => $term->ordering_rank,
                                    'body' => $term->body,
                                ],
                            ];
                        })
                        ->toArray();
                @endphp
                const termsEditData = @json($termsEditData);

                function initEditors() {
                    if (!window.tinymce) return;

                    // Avoid re-init if already initialized
                    if (tinymce.get('add_body') || tinymce.get('edit_body')) return;

                    tinymce.init({
                        selector: '#add_body, #edit_body',
                        plugins: [
                            // Core editing features
                            'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'link', 'lists', 'media',
                            'searchreplace', 'table', 'visualblocks', 'wordcount',
                            // Premium features (available via your Tiny Cloud key)
                            'checklist', 'mediaembed', 'casechange', 'formatpainter', 'pageembed', 'a11ychecker',
                            'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'advtemplate',
                            'ai',
                            'uploadcare', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags',
                            'autocorrect', 'typography', 'inlinecss', 'markdown', 'importword', 'exportword',
                            'exportpdf'
                        ],
                        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography uploadcare | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
                        tinycomments_mode: 'embedded',
                        tinycomments_author: 'Author name',
                        mergetags_list: [{
                                value: 'First.Name',
                                title: 'First Name'
                            },
                            {
                                value: 'Email',
                                title: 'Email'
                            },
                        ],
                        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject(
                            'See docs to implement AI Assistant')),
                        uploadcare_public_key: '0d3531e695b75d636586',
                        height: 320,
                        menubar: false,
                        branding: false
                    });
                }

                function setEditorContent(textareaId, html) {
                    if (!window.tinymce) {
                        const textarea = document.getElementById(textareaId);
                        if (textarea) textarea.value = html || '';
                        return;
                    }
                    const editor = tinymce.get(textareaId);
                    if (editor) {
                        editor.setContent(html || '');
                        return;
                    }
                    initEditors();
                    // Delay slightly for async init
                    setTimeout(() => {
                        if (!window.tinymce) return;
                        const ed = tinymce.get(textareaId);
                        if (ed) ed.setContent(html || '');
                    }, 300);
                }

                function editfunc(id) {
                    const row = termsEditData[id];
                    if (!row) return;
                    document.getElementById('edit_id').value = row.id;
                    document.getElementById('edit_title').value = row.title || '';
                    document.getElementById('edit_ordering_rank').value = row.ordering_rank ?? '';
                    $("#edit_modal").modal('show');
                    setTimeout(() => setEditorContent('edit_body', row.body || ''), 150);
                }

                function deletefunc(id) {
                    document.getElementById('deleteid').value = id;
                    $("#delete_modal").modal('show');
                }

                document.addEventListener('DOMContentLoaded', function() {
                    if (!window.tinymce) {
                        const warning = document.getElementById('tinymceWarning');
                        if (warning) warning.classList.remove('d-none');
                    }
                    initEditors();

                    if (window.jQuery && $('#termsTable').length) {
                        $('#termsTable').DataTable({
                            pageLength: 25
                        });
                    }

                    ['addTermForm', 'editTermForm'].forEach((formId) => {
                        const form = document.getElementById(formId);
                        if (!form) return;
                        form.addEventListener('submit', function() {
                            if (window.tinymce) {
                                tinymce.triggerSave();
                            }
                        });
                    });
                });
            </script>
@endsection
