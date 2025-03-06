@extends('mantis.layouts.app')

@section('besogo-css')
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/besogo.css') }}>
    <link rel="stylesheet" type="text/css" href={{ asset('besogo/css/board-wood.css') }} id="theme">
	<link rel="stylesheet" href={{ asset('css/besogo_custom.css') }} >

@endsection

@section('main')


    <div class="mt-2">
        @if($problem->title)
            <div class="card-header problem-title">
                {{ $problem->title}}
            </div>
        @endif
        <div class="card-body problem-box">
            @php
                $setColor = "black-firstMove bg-black-firstMove";
                if ($problem->first_move == 'white') $setColor = "white-firstMove bg-white-firstMove";
            @endphp
            <div class="besogo-box">
                <div class="problem-firstMove {{ $setColor }} ">
                    <span>{{ $problem->first_move }}</span>
                </div>
                <div class="besogo-editor besogo-topic" resize="fixed" realstones="on" coord="western" panels="" nokeys ="true" nowheel="true" orient="portrait" maxwidth="500"></div>
            </div>
            
            <div class="problem-option-box">
                <div class="card-body">

                    <div class="problem-topic-name mb-4">{{ $problem->topic->name }}</div>

                    <div class="problem-info mb-2">
                        <span>Độ khó :</span> 
                        <span>{{ $problem->level->name }} ({{ $problem->level->value }})</span>
                    </div>

                    <div class="problem-info mb-2">
                        <span>Bộ sưu tập : </span>
                        <span>{{ $problem->album->name }}</span>
                    </div>

                    <div class="problem-info mb-2">
                        <span>Tác giả : </span>
                        <span>{{ $problem->user->name }}</span>
                    </div>

                    <div class="problem-info mb-2">
                        <span>Ngày đăng : </span>
                        <span>{{ $problem->created_at }}</span>
                    </div>
                    {{-- <div class="problem-info mb-2">
                        <span>Ngày cập nhật : </span>
                        <span>{{ $problem->updated_at }}</span>
                    </div> --}}
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
<script src={{ asset('besogo/js/custom.js') }}></script>

<script type="text/javascript">


    document.addEventListener("DOMContentLoaded", function() {
        const GM_GOODMOVE = 'GM_GOODMOVE';
        const BM_BADMOVE = 'BM_BADMOVE';

        var problemData = @json($problem);
        var sgfData = problemData.result;
        var firstMoveColor = problemData.first_move === "black" ? -1 : 1; 

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
                var sgfDefault = besogo.parseSgf(sgfData);
                besogo.loadSgf(sgfDefault, editor);

                //đổi màu nước đi đầu tiên
                setFirstMove(true,firstMoveColor,editor);
                redrawAll(editor.getCurrent());

                // function calculateViewBox(sgf) {
                //     if (typeof COORD_MARGIN === "undefined") {
                //         var COORD_MARGIN = 75;
                //     }
                //     if (typeof EXTRA_MARGIN === "undefined") {
                //         var EXTRA_MARGIN = 6; 
                //     }
                //     if (typeof CELL_SIZE === "undefined") {
                //         CELL_SIZE = 88; // Giá trị mặc định
                //     }
                //     BOARD_MARGIN = (editor.getCoordStyle() === "none" ? 0 : COORD_MARGIN) + EXTRA_MARGIN;
                //     var currentSize = editor.getCurrent().getSize();
                //     var sizeX = currentSize.x;
                //     var sizeY = currentSize.y;
                //     var boardWidth = 2 * BOARD_MARGIN + sizeX * CELL_SIZE;
                //     var boardHeight = 2 * BOARD_MARGIN + sizeY * CELL_SIZE;
                //     var minX = sizeX, minY = sizeY, maxX = 0, maxY = 0;

                //     // 1️⃣ Lấy tất cả các nước đi ";B[xx]" và ";W[xx]"
                //     var moveMatches = sgf.match(/;[BW]\[[a-z]{2}\]/g) || [];

                //     // 2️⃣ Lấy tất cả các quân cờ đặt sẵn "AB[xx][xx]" và "AW[xx][xx]"
                //     var setupMatches = sgf.match(/\b(AW|AB)(\[[a-z]{2}\])+/g) || [];

                //     var allMatches = [...moveMatches];

                //     // 3️⃣ Tách từng tọa độ từ "AB[xx][xx]" và "AW[xx][xx]"
                //     setupMatches.forEach(setup => {
                //         var positions = setup.match(/\[[a-z]{2}\]/g); // Lấy tất cả [xx]
                //         if (positions) {
                //             allMatches.push(...positions);
                //         }
                //     });

                //     // 4️⃣ Duyệt qua tất cả tọa độ để tìm minX, minY, maxX, maxY
                //     allMatches.forEach(match => {
                //         var pos = match.match(/[a-z]{2}/)[0]; // Lấy tọa độ quân cờ
                //         var x = pos.charCodeAt(0) - 97; // 'a' -> 0, 'b' -> 1, ...
                //         var y = pos.charCodeAt(1) - 97;

                //         minX = Math.min(minX, x);
                //         minY = Math.min(minY, y);
                //         maxX = Math.max(maxX, x);
                //         maxY = Math.max(maxY, y);
                //     });

                //     // Nếu không có quân cờ nào, hiển thị toàn bộ bàn cờ
                //     if (minX > maxX || minY > maxY) {
                //         return `0 0 ${boardWidth} ${boardHeight}`;
                //     }
                //     console.log(minX,maxX,minY,maxY);
                //     // Tính điểm giữa của bàn cờ
                //     var centerX = sizeX / 2;
                //     var centerY = sizeY / 2;

                //     // Kiểm tra xem quân cờ có nằm quá nửa bàn cờ không
                //     var occupiesMoreThanHalf = (
                //         minX <= centerX && maxX >= centerX || 
                //         minY <= centerY && maxY >= centerY
                //     );

                //     if (occupiesMoreThanHalf) {
                //         return `0 0 ${boardWidth} ${boardHeight}`;
                //     }

                //     // Định nghĩa khoảng margin để hiển thị đủ tọa độ
                //     var offsetX = BOARD_MARGIN; // Khoảng trống để chứa chữ số
                //     var offsetY = BOARD_MARGIN;

                //     // Xác định tọa độ bắt đầu của viewBox
                //     var viewX = Math.max(0, minX * CELL_SIZE - offsetX);
                //     var viewY = Math.max(0, minY * CELL_SIZE - offsetY);

                //     // Xác định chiều rộng và chiều cao của viewBox, không vượt quá kích thước bàn cờ
                //     var viewWidth = Math.min(boardWidth - viewX, (maxX - minX + 3) * CELL_SIZE + offsetX);
                //     var viewHeight = Math.min(boardHeight - viewY, (maxY - minY + 3) * CELL_SIZE + offsetY);

                //     // Đảm bảo viewBox không vượt khỏi bàn cờ
                //     viewWidth = Math.min(viewWidth, boardWidth - viewX);
                //     viewHeight = Math.min(viewHeight, boardHeight - viewY);


                //     //tạo view hình vuông cho bàn cờ
                //     var viewBoxSize = Math.max(viewWidth, viewHeight);

                //     return `${viewX} ${viewY} ${viewBoxSize} ${viewBoxSize}`;
                // }

                function detectCorner(minX, minY, maxX, maxY, sizeX, sizeY) {
                    let centerX = sizeX / 2;
                    let centerY = sizeY / 2;

                    if (maxX < centerX && maxY < centerY) return "topLeft";
                    if (minX >= centerX && maxY < centerY) return "topRight";
                    if (maxX < centerX && minY >= centerY) return "bottomLeft";
                    if (minX >= centerX && minY >= centerY) return "bottomRight";

                    return "center";
                }

                function calculateViewBox(sgf) {
                    if (typeof COORD_MARGIN === "undefined") COORD_MARGIN = 75;
                    if (typeof EXTRA_MARGIN === "undefined") EXTRA_MARGIN = 6;
                    if (typeof CELL_SIZE === "undefined") CELL_SIZE = 88;

                    BOARD_MARGIN = (editor.getCoordStyle() === "none" ? 0 : COORD_MARGIN) + EXTRA_MARGIN;
                    let { x: sizeX, y: sizeY } = editor.getCurrent().getSize();

                    let minX = sizeX, minY = sizeY, maxX = 0, maxY = 0;

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


                    if (minX > maxX || minY > maxY) {
                        return `0 0 1834 1834`; // Không có quân cờ
                    }

                    let corner = detectCorner(minX, minY, maxX, maxY, sizeX, sizeY);
                    console.log(minX, maxX , minY , maxY);
                    console.log('góc',corner);
                    let range = Math.max(maxX - minX, maxY - minY) + 3;
                    
                    minXY = Math.min(minX,minY) - 2;
                    maxXY = Math.max(minX,minY) + 2;

                    let boxSizeMinXY = minXY * CELL_SIZE;
                    let boxSizeMaxXY = maxXY * CELL_SIZE;
                    let sizeCorner = 1834;
                    switch (corner) {
                        case "topLeft":
                            let maxCornerA = Math.max(maxX,maxY) + 2;
                            sizeCorner = maxCornerA * CELL_SIZE + 2* BOARD_MARGIN;
                            console.log('maxCornerA là',maxCornerA);

                            return `0 0 ${sizeCorner} ${sizeCorner}`;

                        case "topRight":
                            let maxCornerB = 18 - (Math.max((18-minX),maxY) + 1);
                            sizeCorner = maxCornerB * CELL_SIZE;
                            console.log('maxCornerB là',maxCornerB);

                            return `${sizeCorner } 0  ${1834 - sizeCorner} ${1834 - sizeCorner}`;

                        case "bottomLeft":
                            let maxCornerC = 18 - (Math.max(maxX,(18-minY)) + 1);
                            sizeCorner = maxCornerC * CELL_SIZE;
                            console.log('maxCornerC là',maxCornerC);

                            return `0 ${sizeCorner} ${1834 - sizeCorner} ${1834 - sizeCorner}`;

                        case "bottomRight":
                            let maxCornerD = Math.min(minX,minY) - 1;
                            sizeCorner = maxCornerD * CELL_SIZE;
                            console.log('maxCornerD là',maxCornerD);

                            return `${sizeCorner} ${sizeCorner} ${1834 - sizeCorner} ${1834 - sizeCorner}`;
                        case "center":
                        default:
                            return `0 0 ${sizeCorner} ${sizeCorner}`;
                    }
                }

                function updateViewBox() {
                    var viewBoxValue = calculateViewBox(sgfData);
                    var svg = document.querySelector(".besogo-board svg");
                    svg.setAttribute("viewBox", viewBoxValue);
                }
                editor.addListener(updateViewBox);
                updateViewBox();

                //ẩn hiển thị nước đi tiếp theo
                editor.toggleVariantStyle(true);
                editor.setVariantStyle(2);

                //event
                let gameStopped = false; 
                editor.addListener(() => {
                    if (gameStopped) return; 
                    let currentNode = editor.getCurrent();
                    let lastMove = currentNode.move; // Lấy nước đi cuối cùng

                    if (lastMove) {
                        if (currentNode.comment) {
                            if (currentNode.comment.includes(GM_GOODMOVE)) {
                                showMoveFeedback("Tuyệt vời! Đây là nước đi tốt!", "success");
                                gameStopped = true;
                                return;
                            } else if (currentNode.comment.includes(BM_BADMOVE)) {
                                showMoveFeedback("Sai lầm! Hãy thử lại.", "danger");
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
