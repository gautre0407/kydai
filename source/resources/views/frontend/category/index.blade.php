@extends('frontend.layouts.app')

@section('besogo-css')

    <link rel="stylesheet" type="text/css" href="besogo/css/besogo.css">
    <link rel="stylesheet" type="text/css" href="besogo/css/board-wood.css" id="theme">
@endsection

@section('content')

    <div>
        <div class="besogo-editor" style="height: 525px; width: 960px; flex-direction: row;" resize="fixed" realstones="on" coord="western"></div>
        <div class="card">
            <div class="card-body">
                <div class="sgf-box">
                    <label>SGF:</label>
                    <textarea id="sgfOutput" readonly rows="5"></textarea>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('besogo-js')
<script src="besogo/js/besogo.js"></script>
<script src="besogo/js/editor.js"></script>
<script src="besogo/js/gameRoot.js"></script>
<script src="besogo/js/svgUtil.js"></script>
<script src="besogo/js/parseSgf.js"></script>
<script src="besogo/js/loadSgf.js"></script>
<script src="besogo/js/saveSgf.js"></script>
<script src="besogo/js/boardDisplay.js"></script>
<script src="besogo/js/coord.js"></script>
<script src="besogo/js/toolPanel.js"></script>
<script src="besogo/js/filePanel.js"></script>
<script src="besogo/js/controlPanel.js"></script>
<script src="besogo/js/namesPanel.js"></script>
<script src="besogo/js/commentPanel.js"></script>
<script src="besogo/js/treePanel.js"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        var toggleTheme;
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

        let sgfContentDefault = "(;FF[4]GM[1]CA[UTF-8]AP[besogo:0.0.2-alpha]SZ[19]ST[0];B[pd];W[dp];B[pq];W[dd];B[fq];W[cn](;B[jp];W[po](;B[qo];W[qn];B[qp];W[pn];B[np];W[pj];B[qh];W[qc];B[pc];W[qd];B[qf];W[qe];B[pe];W[rf];B[rg];W[pf];B[qg];W[pb];B[ob];W[qb];B[nc];W[of];B[oh];W[nd];B[lc];W[le])(;B[pl];W[mp];B[oo];W[on];B[no];W[pp];B[oq];W[qq];B[qr];W[op];B[np];W[nq];B[mo];W[mq];B[pn];W[pm];B[qn];W[qm]))(;B[dq];W[cq];B[cr];W[eq];B[dr];W[fp];B[er];W[ep];B[gq](;W[qo];B[np])(;W[po];B[qo];W[qn];B[qp];W[pn];B[nq])(;W[dj];B[jp];W[nc];B[qf];W[jd])))";

        if (typeof besogo !== "undefined" && besogo.autoInit) {

            let editors = besogo.autoInit(); // Khởi tạo các editor
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
                function updateSgf() {
                    let sgfContent = besogo.composeSgf(editor);
                    // console.log("SGF ván cờ:", sgfContent);
                    document.getElementById("sgfOutput").value = sgfContent;
                }

                var sgf = besogo.parseSgf(sgfContentDefault); // Phân tích SGF
                besogo.loadSgf(sgf, editor);

                editor.addListener(updateSgf); 
                updateSgf();


                const sgfTree = editor.getRoot(); // Lấy gốc của SGF
                const movesList = [];

                // Duyệt qua các nước đi trong SGF và lưu lại
                // function traverse(node) {
                //     if (node.move) {
                //         movesList.push({
                //             x: node.move.x,
                //             y: node.move.y,
                //             color: node.move.color,
                //         });
                //     }
                //     node.children.forEach(child => traverse(child));
                // }


                let isProcessing = false;

let movesMap = new Map(); // Lưu nước đi theo (x, y) để tra cứu nhanh

// Duyệt qua tất cả các nước đi trong SGF, lưu theo tọa độ
function traverse(node, parentMoves = []) {
    if (node.move) {
        const moveKey = `${node.move.x},${node.move.y},${node.move.color}`;
        movesMap.set(moveKey, { node, parentMoves: [...parentMoves, node] });
    }
    node.children.forEach(child => traverse(child, [...parentMoves, node]));
}
traverse(sgfTree);

// Hàm tìm nước đi hiện tại trong SGF
function findMoveInSGF(x, y, color) {
    return movesMap.get(`${x},${y},${color}`);
}

editor.addListener(function (event) {
    if (isProcessing) return; // Ngăn chặn chạy liên tục

    let lastMove = editor.getCurrent().move; // Lấy nước đi hiện tại
    if (!lastMove) return;

    // Kiểm tra nước đi có tồn tại trong SGF không
    const currentMoveNode = findMoveInSGF(lastMove.x, lastMove.y, lastMove.color);
    if (!currentMoveNode) {
        console.log("❌ Nước đi không hợp lệ trong SGF!");
        return;
    }

    console.log("✅ Đen đi đúng nước:", lastMove);

    // Lấy danh sách nhánh con (các biến thể)
    const variants = currentMoveNode.node.children;

    // Nếu có biến thể cho trắng, chọn ngẫu nhiên
    const whiteVariants = variants.filter(v => v.move && v.move.color === 1);
    if (whiteVariants.length > 0) {
        isProcessing = true;
        setTimeout(() => {
            const nextMove = whiteVariants[Math.floor(Math.random() * whiteVariants.length)];
            editor.setCurrent(nextMove);
            console.log("🎯 Trắng tự động đi:", nextMove.move);
            isProcessing = false;
        }, 500);
    }
});








                // let isProcessing = false;

                // editor.addListener(function (event) {

                //     if (isProcessing) return;

                //     let lastMove = editor.getCurrent().move; // Lấy nước đi mới nhất
                //     if (!lastMove) return;

                //     // Lấy index nước đi hiện tại trong SGF
                //     const currentIndex = editor.getCurrent().moveNumber - 1;
                    
                //     // Kiểm tra nếu index hợp lệ
                //     if (currentIndex < 0 || currentIndex >= movesList.length) return;
                //     const expectedMove = movesList[currentIndex]; // Nước đi mong đợi trong SGF

                //     // Kiểm tra ĐEN đi đúng nước theo SGF
                //     if (lastMove.x === expectedMove.x && lastMove.y === expectedMove.y && lastMove.color === expectedMove.color) {
                //         console.log("✅ Đen đi đúng nước theo SGF:", lastMove);
                        
                //         // Lấy nước đi tiếp theo của Trắng
                //         const nextMove = movesList[currentIndex + 1];

                //         if (nextMove && nextMove.color === 1) { // Nếu nước tiếp theo là Trắng
                //             isProcessing = true;
                //             setTimeout(() => { 
                //                 const nextNode = editor.getVariants()?.[0]; // Lấy nhánh đầu tiên
                //                 if (nextNode) {
                //                     editor.setCurrent(nextNode);
                //                     console.log("🎯 Trắng tự động đi:", nextNode.move);
                //                 } else {
                //                     console.log("⚠️ Không có biến thể nào để đi tiếp!");
                //                 }

                //                 isProcessing = false; // Mở lại sau khi hoàn tất
                //             }, 500);
                //         }
                //     } else {
                //         console.log("❌ Nước đi sai! Đen phải đi đúng theo SGF.");
                //     }
                // });

            } else {
                console.error("Không tìm thấy editor! Kiểm tra class hoặc cách khởi tạo.");
            }

        } else {
            console.error("besogo.autoInit không tồn tại hoặc chưa được tải!");
        }


        
    });

</script>

@endsection

