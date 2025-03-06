@extends('mantis.layouts.app')

@section('besogo-css')
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/besogo.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/board-wood.css') }} id="theme">
	<link rel="stylesheet" href={{ asset('css/besogo_custom.css') }} >
@endsection

@section('main')

    <div>
        
        <div class="card">
            <div class="card-header">
                Tạo đáp án cho bài tập
            </div>
            <div class="card-body problem-box">
                <div class="besogo-box">
                    <div class="besogo-editor besogo-topic"  resize="fixed" realstones="on" coord="western" orient="portrait" maxwidth="500" panels="control+tool+tree"></div>  
                </div>
                
                <div class="card problem-option-box">
                    
                    <div class="card-body">
                        <div class="btn-problem-box mb-3">
                            <button id="markCorrect" class="btn btn-success btn-problem-tick">Đúng (✔)</button>
                            <button id="markWrong" class="btn btn-danger btn-problem-tick">Sai (✖)</button>
                        </div>
                        <textarea id="commentInput" class="form-control mb-3" placeholder="Nhập bình luận..."></textarea>
                        <form action="{{ route('problem.result_save',$problem->id) }}" method="POST" class="mb-3">
                        @csrf
                        {{-- <div class="card-footer"> --}}
                            <textarea id="sgfOutput" readonly class="form-control mb-3" name="result" rows="5" hidden></textarea>
                            <button type="submit" class="btn btn-primary">Lưu</button>
                        {{-- </div> --}}
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>

@endsection

@section('besogo-js')
<script src={{ asset('besogo/js/besogo.js') }}></script>
<script src={{ asset('besogo/js/editor.js') }}></script>
<script src={{ asset('besogo/js/gameRoot.js') }}></script>
<script src={{ asset('besogo/js/svgUtil.js') }}></script>
<script src={{ asset('besogo/js/parseSgf.js') }}></script>
<script src={{ asset('besogo/js/loadSgf.js') }}></script>
<script src={{ asset('besogo/js/saveSgf.js') }}></script>
<script src={{ asset('besogo/js/boardDisplay.js') }}></script>
<script src={{ asset('besogo/js/coord.js') }}></script>
<script src={{ asset('besogo/js/toolPanel.js') }}></script>
<script src={{ asset('besogo/js/filePanel.js') }}></script>
<script src={{ asset('besogo/js/controlPanel.js') }}></script>
<script src={{ asset('besogo/js/namesPanel.js') }}></script>
<script src={{ asset('besogo/js/commentPanel.js') }}></script>
<script src={{ asset('besogo/js/treePanel.js') }}></script>
<script src={{ asset('besogo/js/custom.js') }}></script>

<script type="text/javascript">

    document.addEventListener("DOMContentLoaded", function() {
        const GM_GOODMOVE = 'GM_GOODMOVE';
        const BM_BADMOVE = 'BM_BADMOVE';

        var problemData = @json($problem);
        var sgfData = problemData.question;
        var firstMoveColor = problemData.first_move === "black" ? -1 : 1; 
        document.getElementById("markCorrect").addEventListener("click", function() {
            markMove(GM_GOODMOVE);
        });
        document.getElementById("markWrong").addEventListener("click", function() {
            markMove(BM_BADMOVE);
        });

        const commentInput = document.getElementById('commentInput');
        if (typeof besogo !== "undefined" && besogo.autoInit) {

            let editors = besogo.autoInit();
            let editor;
           
            if (Array.isArray(editors) && editors.length > 0) {
                editor = editors[0]; // Lấy editor đầu tiên nếu autoInit trả về danh sách
            } else {
                let besogoElement = document.querySelector(".besogo-editor");
                if (besogoElement && besogoElement.besogoEditor) {
                    editor = besogoElement.besogoEditor; // Tìm editor trong DOM
                }
            }
            
            if (editor) {

                var sgf = besogo.parseSgf(sgfData); // Phân tích SGF
                besogo.loadSgf(sgf, editor);//load sgf vào game

                editor.toggleVariantStyle(true);//vô hiệu hóa hiển thị biến

                //đổi màu nước đi đầu tiên
                setFirstMove(true,firstMoveColor,editor);
                redrawAll(editor.getCurrent());

                // update nội dung sgf vào input
                bindUpdateSgf(editor, "sgfOutput", besogo);

                //remove 1 số nút k cần và vô hiệu hóa ctr + click
                removeElements(
                    [
                        ".setBlack",
                        ".setWhite",
                        'button[title="Toggle coordinates"]',
                        'button[title="Variants: [child]/sibling"]',
                        'button[title="Variants: [show]/hide"]',
                        'button[title="Set empty point"]',
                        'button[title="Clear mark"]',
                        'input[title="Pass move"]',
                        'input[title="Raise variation"]',
                        'input[title="Lower variation"]',
                    ],
                    editor
                );

                function markMove(newComment) {
                    let node = editor.getCurrent();
                    if (!node) return;

                    let comments = node.comment ? node.comment.split("\n") : [];
                    let hasGoodMove = comments.includes(GM_GOODMOVE);
                    let hasBadMove = comments.includes(BM_BADMOVE);

                    // Nếu đã có comment đúng với loại đang chọn, thì xóa nó đi
                    if ((newComment === GM_GOODMOVE && hasGoodMove) || 
                        (newComment === BM_BADMOVE && hasBadMove)) {
                        comments = comments.filter(c => c !== newComment);
                    } 
                    // Nếu đang có đánh dấu khác, chuyển sang loại mới
                    else if (newComment === GM_GOODMOVE && hasBadMove) {
                        comments = comments.filter(c => c !== BM_BADMOVE);
                        comments.push(GM_GOODMOVE);
                    } 
                    else if (newComment === BM_BADMOVE && hasGoodMove) {
                        comments = comments.filter(c => c !== GM_GOODMOVE);
                        comments.push(BM_BADMOVE);
                    } 
                    // Nếu chưa có đánh dấu nào, thêm mới
                    else {
                        comments.push(newComment);
                    }

                    // Cập nhật lại comment vào node
                    node.comment = comments.length > 0 ? comments.join("\n") : "";
                    editor.setComment(node.comment);
                    editor.setCurrent(node);

                    updateNavTreeColors(); // Cập nhật màu sắc trên cây sơ đồ
                }

                function updateNavTreeColors() {
                    let currentMove = editor.getCurrent(); // Lấy nước đi hiện tại

                    function traverseTree(node) {
                        if (!node || !node.navTreeMarker) return;

                        let comment = node.comment || "";
                        let isCurrent = node === currentMove;

                        // Xóa class cũ trước khi thêm mới
                        node.navTreeMarker.classList.remove("move-good", "move-bad", "move-good-old", "move-bad-old");

                        if (comment.includes(GM_GOODMOVE)) {
                            if (isCurrent) {
                                node.navTreeMarker.classList.add("move-good"); // Nước hiện tại có viền xanh đậm
                            } else {
                                node.navTreeMarker.classList.add("move-good-old"); // Nước cũ có viền xanh nhạt
                            }
                        } else if (comment.includes(BM_BADMOVE)) {
                            if (isCurrent) {
                                node.navTreeMarker.classList.add("move-bad"); // Nước hiện tại có viền đỏ đậm
                            } else {
                                node.navTreeMarker.classList.add("move-bad-old"); // Nước cũ có viền đỏ nhạt
                            }
                        }
                        
                        node.children.forEach(traverseTree);
                    }

                    traverseTree(editor.getRoot());
                }

                // Lặp lại việc cập nhật mỗi khi di chuyển nước đi
                editor.addListener(updateNavTreeColors);
                updateNavTreeColors();

               editor.addListener(() => {
                let currentNode = editor.getCurrent();
                if (!currentNode) return;
                    
                    let comments = currentNode.comment ? currentNode.comment.split("\n") : [];
                    let filteredComments = comments.filter(c => c !== GM_GOODMOVE && c !== BM_BADMOVE);

                    commentInput.value = filteredComments.join("\n"); // Show đúng comment mà không ghi đè
                });

                let saveTimeout = null;

                commentInput.addEventListener("input", function () {
                    let currentNode = editor.getCurrent();
                    if (!currentNode) return;

                    // Xóa timeout cũ
                    if (saveTimeout) clearTimeout(saveTimeout);

                    saveTimeout = setTimeout(() => {
                        let comments = currentNode.comment ? currentNode.comment.split("\n") : [];
                        
                        // Lọc GM_GOODMOVE và BM_BADMOVE (giữ nguyên các đánh dấu)
                        let specialComments = comments.filter(c => c === GM_GOODMOVE || c === BM_BADMOVE);
                        let newComment = commentInput.value.trim();

                        // Chỉ cập nhật comment người dùng, không ảnh hưởng đánh dấu
                        let filteredComments = specialComments;
                        if (newComment) {
                            filteredComments.push(newComment);
                        }

                        // Gán lại comment vào node
                        currentNode.comment = filteredComments.join("\n");
                        editor.setComment(currentNode.comment);
                        editor.setCurrent(currentNode);

                        console.log("Comment đã lưu:", currentNode.comment);
                    }, 1000);
                });



            } else {
                console.error("Không tìm thấy editor! Kiểm tra class hoặc cách khởi tạo.");
            }

        } else {
            console.error("besogo.autoInit không tồn tại hoặc chưa được tải!");
        } 
    });

</script>

<script>
    
    
    
    </script>

@endsection
