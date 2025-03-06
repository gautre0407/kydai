@extends('frontend.layouts.app')

@section('besogo-css')
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/besogo.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/board-wood.css') }} id="theme">
@endsection

@section('content')

    <div>
        
        <div class="card">
            <div class="card-header">
                Làm bài tập
            </div>
            <div class="card-body" style="display: flex;justify-content: center;">
                <div style="width:600px;">
                    <div class="besogo-editor" resize="auto" realstones="on" coord="western" panels="" nokeys ="true" nowheel="true"></div>
                </div>
            </div>
        </div>
    </div>


<!-- Modal thông báo -->
<div class="modal fade" id="moveFeedbackModal" tabindex="-1" aria-labelledby="moveFeedbackLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moveFeedbackLabel">Kết quả nước đi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="moveFeedbackText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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

<script type="text/javascript">


    document.addEventListener("DOMContentLoaded", function() {
        var toggleTheme;
        var problemData = @json($problem->result);

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
                editor = editors[0];
            } else {
                let besogoElement = document.querySelector(".besogo-editor");
                if (besogoElement && besogoElement.besogoEditor) {
                    editor = besogoElement.besogoEditor;
                }
            }
            
            if (editor) {
                
                //load sgf
                var sgfDefault = besogo.parseSgf(problemData);
                besogo.loadSgf(sgfDefault, editor);

                function calculateViewBox(sgf) {
                    if (typeof COORD_MARGIN === "undefined") {
                        var COORD_MARGIN = 75;
                    }
                    if (typeof EXTRA_MARGIN === "undefined") {
                        var EXTRA_MARGIN = 6; 
                    }
                    if (typeof CELL_SIZE === "undefined") {
                        CELL_SIZE = 88; // Giá trị mặc định
                    }
                    BOARD_MARGIN = (editor.getCoordStyle() === "none" ? 0 : COORD_MARGIN) + EXTRA_MARGIN;
                    var currentSize = editor.getCurrent().getSize();
                    var sizeX = currentSize.x;
                    var sizeY = currentSize.y;

                    var boardWidth = 2 * BOARD_MARGIN + sizeX * CELL_SIZE;
                    var boardHeight = 2 * BOARD_MARGIN + sizeY * CELL_SIZE;
                    var minX = sizeX, minY = sizeY, maxX = 0, maxY = 0;

                    // 1️⃣ Lấy tất cả các nước đi ";B[xx]" và ";W[xx]"
                    var moveMatches = sgf.match(/;[BW]\[[a-z]{2}\]/g) || [];

                    // 2️⃣ Lấy tất cả các quân cờ đặt sẵn "AB[xx][xx]" và "AW[xx][xx]"
                    var setupMatches = sgf.match(/\b(AW|AB)(\[[a-z]{2}\])+/g) || [];

                    var allMatches = [...moveMatches];

                    // 3️⃣ Tách từng tọa độ từ "AB[xx][xx]" và "AW[xx][xx]"
                    setupMatches.forEach(setup => {
                        var positions = setup.match(/\[[a-z]{2}\]/g); // Lấy tất cả [xx]
                        if (positions) {
                            allMatches.push(...positions);
                        }
                    });

                    // 4️⃣ Duyệt qua tất cả tọa độ để tìm minX, minY, maxX, maxY
                    allMatches.forEach(match => {
                        var pos = match.match(/[a-z]{2}/)[0]; // Lấy tọa độ quân cờ
                        var x = pos.charCodeAt(0) - 97; // 'a' -> 0, 'b' -> 1, ...
                        var y = pos.charCodeAt(1) - 97;

                        minX = Math.min(minX, x);
                        minY = Math.min(minY, y);
                        maxX = Math.max(maxX, x);
                        maxY = Math.max(maxY, y);
                    });

                    // Nếu không có quân cờ nào, hiển thị toàn bộ bàn cờ
                    if (minX > maxX || minY > maxY) {
                        return `0 0 ${boardWidth} ${boardHeight}`;
                    }

                    // Tính điểm giữa của bàn cờ
                    var centerX = sizeX / 2;
                    var centerY = sizeY / 2;

                    // Kiểm tra xem quân cờ có nằm quá nửa bàn cờ không
                    var occupiesMoreThanHalf = (
                        minX <= centerX && maxX >= centerX || 
                        minY <= centerY && maxY >= centerY
                    );

                    if (occupiesMoreThanHalf) {
                        return `0 0 ${boardWidth} ${boardHeight}`;
                    }

                    // Định nghĩa khoảng margin để hiển thị đủ tọa độ
                    var offsetX = BOARD_MARGIN; // Khoảng trống để chứa chữ số
                    var offsetY = BOARD_MARGIN;

                    // Xác định tọa độ bắt đầu của viewBox
                    var viewX = Math.max(0, minX * CELL_SIZE - offsetX);
                    var viewY = Math.max(0, minY * CELL_SIZE - 2*offsetY);

                    // Xác định chiều rộng và chiều cao của viewBox, không vượt quá kích thước bàn cờ
                    var viewWidth = Math.min(boardWidth - viewX, (maxX - minX + 3) * CELL_SIZE + 2 * offsetX);
                    var viewHeight = Math.min(boardHeight - viewY, (maxY - minY + 3) * CELL_SIZE + 2 * offsetY);

                    // Đảm bảo viewBox không vượt khỏi bàn cờ
                    viewWidth = Math.min(viewWidth, boardWidth - viewX);
                    viewHeight = Math.min(viewHeight, boardHeight - viewY);


                    //tạo view hình vuông cho bàn cờ
                    var viewBoxSize = Math.max(viewWidth, viewHeight);

                    return `${viewX} ${viewY} ${viewBoxSize} ${viewBoxSize}`;
                }

                function updateViewBox() {
                    var viewBoxValue = calculateViewBox(problemData);
                    var svg = document.querySelector(".besogo-board svg");
                    svg.setAttribute("viewBox", viewBoxValue);
                }

                editor.addListener(updateViewBox);
                updateViewBox();

                //hide variants
                editor.toggleVariantStyle(true);
                editor.setVariantStyle(2);


                //event
                let gameStopped = false; 
                editor.addListener(() => {
                    if (gameStopped) return; 
                    let currentNode = editor.getCurrent();
                    let lastMove = currentNode.move; // Lấy nước đi cuối cùng

                    if (lastMove) {
                        // if (currentNode.comment && (currentNode.comment.includes("Good Move") || currentNode.comment.includes("Bad Move"))) {
                        //     console.log(currentNode.comment); // Hiển thị comment
                        //     gameStopped = true;
                        //     return;
                        // }
                        if (currentNode.comment) {
                            if (currentNode.comment.includes("Good Move")) {
                                showMoveFeedback("Tuyệt vời! Đây là nước đi tốt!", "success");
                                console.log('good')
                                gameStopped = true;
                                return;
                            } else if (currentNode.comment.includes("Bad Move")) {
                                showMoveFeedback("Sai lầm! Hãy thử lại.", "danger");
                                console.log('bad')

                                gameStopped = true;
                                return;
                            }
                        }
                    }
                    if (lastMove && lastMove.color === -1) { // Kiểm tra nếu nước đi cuối cùng là đen                        
                        let nextMoveNode = currentNode.children.find(child => child.move?.color === 1);

                        if (nextMoveNode) {
                            setTimeout(() => editor.nextNode(1), 300);
                        } else {
                            console.log("Không chính xác");
                        }
                    }
                    // Hàm hiển thị modal phản hồi nước đi
                    function showMoveFeedback(message, type) {
                        let feedbackText = document.getElementById("moveFeedbackText");
                        feedbackText.innerHTML = message;
                        feedbackText.className = `text-${type}`; // Đổi màu theo kết quả
                    
                        let modalElement = document.getElementById("moveFeedbackModal");
                        let moveFeedbackModal = new bootstrap.Modal(modalElement, { backdrop: true });

                        moveFeedbackModal.show();
                        // Xử lý khi bấm nút đóng
                        modalElement.addEventListener('hidden.bs.modal', function () {
                            console.log("Modal đã đóng");
                        });
                    }
                    let modalElement = document.getElementById("moveFeedbackModal");
                    let moveFeedbackModal = new bootstrap.Modal(modalElement);

                    // Khi bấm nút đóng hoặc nút "X"
                    document.querySelectorAll(".btn-close, .btn-secondary").forEach(btn => {
                        btn.addEventListener("click", function () {
                            moveFeedbackModal.hide();
                            document.getElementById("moveFeedbackModal").classList.remove("show");
                            document.body.classList.remove("modal-open");
                            
                            setTimeout(() => {
                                let backdrops = document.querySelectorAll(".modal-backdrop");
                                backdrops.forEach(backdrop => backdrop.remove());

                                document.body.classList.remove("modal-open");
                            }, 200); // Đợi 300ms để Bootstrap xử lý xong
                            console.log("Modal đã đóng");

                        });
                    });

                });
            } else {
                console.error("Không tìm thấy editor! Kiểm tra class hoặc cách khởi tạo.");
            }

        } else {
            console.error("besogo.autoInit không tồn tại hoặc chưa được tải!");
        } 
    });

</script>

@endsection
