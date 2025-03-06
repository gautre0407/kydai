@extends('frontend.layouts.app')

@section('besogo-css')
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/besogo.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/board-wood.css') }} id="theme">
@endsection

@section('content')

    <div>
        
        <div class="card">
            <div class="card-header">
                Tạo đáp án cho bài tập
            </div>
            <div class="card-body">
                <div class="besogo-editor" style="height: 525px; width: 960px; flex-direction: row;" resize="fixed" realstones="on" coord="western"></div>
                
            </div>
            <div class="card-footer">
                <button id="markCorrect" class="mt-2 btn btn-success">Đánh dấu đúng (✔)</button>
                <button id="markWrong" class="mt-2 btn btn-danger">Đánh dấu sai (✖)</button>
            </div>
        </div>

        



        <form action="{{ route('problem.result_save',$problem->id) }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="sgf-box">
                        <label class="mr-3">SGF:</label>
                        <textarea id="sgfOutput" readonly class="form-control" name="result"></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="mt-2 btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
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

<script type="text/javascript">


    document.addEventListener("DOMContentLoaded", function() {
        var toggleTheme;
        var problemData = @json($problem);
        document.getElementById("markCorrect").addEventListener("click", function() {
            markMove("Good Move");
        });
        document.getElementById("markWrong").addEventListener("click", function() {
            markMove("Bad Move");
        });

        (function() {
            var current = 0,
                themes = "wood flat bold dark book eidogo glift kibitz sensei".split(' '),
                link = document.getElementById('theme');
            toggleTheme = function(){
                current = (current + 1) % themes.length;
                link.href = "css/board-" + themes[current] + ".css";
            }
            return false;
        })();

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
                var sgf = besogo.parseSgf(problemData.question); // Phân tích SGF
                besogo.loadSgf(sgf, editor);

                editor.toggleVariantStyle(true);
                
                // let variantButton = document.querySelector('.toggle-variant-btn');
                //     variantButton.click();
                    
                // function clickVariantButton() {
                //     let variantButton = document.querySelector('.toggle-variant-btn');
                //     variantButton.click();
                //     if (variantButton) {
                //         let event = new MouseEvent("click", { bubbles: true, cancelable: true, view: window });
                //         variantButton.dispatchEvent(event); // Giả lập click như người dùng thật
                //         console.log("Đã click vào nút Variants!");
                //     } else {
                //         console.log("Không tìm thấy nút Variants, thử lại...");
                //         setTimeout(clickVariantButton, 500); // Thử lại sau 500ms
                //     }
                // }
                // clickVariantButton();

                function updateSgf() {
                    let sgfContent = besogo.composeSgf(editor);
                    sgfContent = sgfContent.replace(/\s+/g, ' ');
                    document.getElementById("sgfOutput").value = sgfContent;

                }
                editor.addListener(updateSgf); 
                updateSgf();

                function markMove(newComment) {
                    let node = editor.getCurrent();
                    if (!node) return;

                    let comments = node.comment ? node.comment.split("\n") : [];
                    let hasGoodMove = comments.includes("Good Move");
                    let hasBadMove = comments.includes("Bad Move");

                    // Nếu đã có comment đúng với loại đang chọn, thì xóa nó đi
                    if ((newComment === "Good Move" && hasGoodMove) || 
                        (newComment === "Bad Move" && hasBadMove)) {
                        comments = comments.filter(c => c !== newComment);
                    } 
                    // Nếu đang có đánh dấu khác, chuyển sang loại mới
                    else if (newComment === "Good Move" && hasBadMove) {
                        comments = comments.filter(c => c !== "Bad Move");
                        comments.push("Good Move");
                    } 
                    else if (newComment === "Bad Move" && hasGoodMove) {
                        comments = comments.filter(c => c !== "Good Move");
                        comments.push("Bad Move");
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

                        if (comment.includes("Good Move")) {
                            if (isCurrent) {
                                node.navTreeMarker.classList.add("move-good"); // Nước hiện tại có viền xanh đậm
                            } else {
                                node.navTreeMarker.classList.add("move-good-old"); // Nước cũ có viền xanh nhạt
                            }
                        } else if (comment.includes("Bad Move")) {
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

                // ✅ Lặp lại việc cập nhật mỗi khi di chuyển nước đi
                editor.addListener(updateNavTreeColors);
                updateNavTreeColors();

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
