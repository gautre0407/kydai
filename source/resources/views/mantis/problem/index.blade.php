@extends('mantis.layouts.app')

@section('besogo-css')
    <link rel="stylesheet" type="text/css" href="besogo/css/besogo.css">
    <link rel="stylesheet" type="text/css" href="besogo/css/board-wood.css" id="theme">
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="javascript: void(0)">Ám dạ lâu</a></li>
<li class="breadcrumb-item" aria-current="page">Đề bài tập</li>
@endsection

@section('main')

    <div class="card">
        <div class="card-header">
            <p class="problem-title">Bước 1: Đề bài tập</p>
        </div>
        <div class="card-body problem-box">
            <div class="besogo-box">
                <div class="besogo-editor besogo-topic"  resize="fixed" realstones="on" coord="western" orient="portrait" maxwidth="500" panels="tool+file" nokeys ="true"></div>    
            </div>
            
            <div class="card problem-option-box">
                <form action="{{ route('problem.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <label class="mb-1">Tiêu đề (không bắt buộc)</label>
                        <textarea class="form-control mb-3" name="title" rows="2"></textarea>

                        <label class="mb-1">Chủ đề</label>
                        <select class="form-select mb-3" aria-label="Default select example" name="topic">
                            <option selected value="1">Sống - chết</option>
                            <option value="2">Tesuji</option>
                            <option value="3">Fuseki</option>
                            <option value="4">Quan tử</option>
                            <option value="5">Đả nhập</option>
                        </select>

                        <label class="mb-1">Độ khó</label>
                        <select class="form-select mb-3" aria-label="Default select example" name="level">
                            <option selected value="1">Gà vàng (16k-30k)</option>
                            <option value="2">Sơ cấp (8k-15k)</option>
                            <option value="3">Trung cấp (4k-7k)</option>
                            <option value="4">Cao cấp (1k-3k)</option>
                            <option value="5">Bậc thầy (1D-2D)</option>
                            <option value="6">Chiến thần (3D-4D)</option>
                            <option value="7">Vương giả (5D+++)</option>
                        </select>

                        <label class="mb-1">Bộ sưu tập</label>
                        <input list="album_collection" id="album" name="album" class="form-control mb-3" placeholder="Nhập để tạo mới hoặc chọn bộ sưu tập">
                        <input type="hidden" name="album_id" id="album_id">
                        <datalist id="album_collection">
                        <option value="Gấu Tre" data-id="1"></option>
                        <option value="LeeChangHo" data-id="2"></option>
                        <option value="LeeSedol" data-id="3"></option>
                        </datalist>

                        <label class="mb-1">Phân quyền</label>
                        <select class="form-select mb-3" aria-label="Default select example" name="decentralization">
                            <option selected value="publish">Công khai</option>
                            <option value="limit">Giới hạn</option>
                            <option value="hidden">Ẩn</option>
                        </select>

                        <div class="d-flex mb-3">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="first_move" id="firstMove1" value="black" checked>
                                <label class="form-check-label" for="firstMove1">
                                    Đen đi
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="first_move" id="firstMove2" value="white" >
                                <label class="form-check-label" for="firstMove2">
                                    Trắng đi
                                </label>
                            </div>
                        </div>

                        <div class="d-flex mb-3">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="radio" name="scale" id="scaleBoard1" value="part" checked>
                                <label class="form-check-label" for="scaleBoard1">
                                    Cắt hình
                                </label>
                            </div>
                            <div class="form-check">
                                <input id="scaleBoard2" class="form-check-input" type="radio" name="scale" id="scaleBoard2"  value="full" >
                                <label class="form-check-label" for="scaleBoard2">
                                    Đầy đủ
                                </label>
                            </div>
                        </div>

                        <textarea id="sgfOutput" readonly class="form-control" name="question" hidden></textarea>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="mt-2 btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-footer">
            <p><strong>Chú ý:</strong></p>
            <p>1/ Phân quyền:</p>
            <p>- Công khai: Tất cả người dùng được phép xem và làm bài tập, thêm bài tập vào bộ sưu tập.</p>
            <p>- Giới hạn: Người dùng chỉ có thể xem và làm bài tập.</p>
            <p>- Ẩn: Chỉ người tạo có thể thấy bài tập.</p>
            <p>2/ Cắt hình hay đầy đủ:</p>
            <p>- Cắt hình: Chỉ hiện một phần bàn cờ có bài tập.</p>
            <p>- Đầy đủ: Hiện đầy đủ bàn cờ.</p>
            <p>- Chỉ bàn 19x19 mới có chức năng cắt hình.</p>
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
<script src="besogo/js/custom.js"></script>

<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('album').addEventListener('change', function (e) {
            let options = document.querySelectorAll('#album_collection option');
            let input = e.target.value;
            let albumIdInput = document.getElementById('album_id');
            albumIdInput.value = ''; 

            options.forEach(option => {
                if (option.value === input) {
                    albumIdInput.value = option.getAttribute('data-id');
                }
            });
        });

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

                // update nội dung sgf vào input
                bindUpdateSgf(editor, "sgfOutput", besogo);

                //click button setBlack 
                document.querySelector('.setBlack').click();

                //remove 1 số nút k cần và vô hiệu hóa ctr + click
                removeElements(
                    [
                        ".setBlackWhite",
                        'button[title="Toggle coordinates"]',
                        'button[title="Variants: [child]/sibling"]',
                        'button[title="Variants: [show]/hide"]',
                        'button[title="Set empty point"]',
                        'button[title="Clear mark"]',
                        'input[title="Pass move"]',
                        'input[title="Raise variation"]',
                        'input[title="Lower variation"]',
                        'input[title="Import SGF"]',
                        'input[title="Export SGF"]',
                        'input[title="New custom size board"]',
                        'input[type="file"]',
                    ],
                    editor
                );

                //disable tỉ lệ bàn cờ
                let lastSize = { x: 0, y: 0 };
                editor.addListener(() => {
                    let size = editor.getRoot().getSize();
                    if (size.x !== lastSize.x || size.y !== lastSize.y) {
                        document.dispatchEvent(new CustomEvent("boardResize", { detail: size }));
                        lastSize = size;
                        let scaleBoard1 = document.getElementById('scaleBoard1');
                        let scaleBoard2 = document.getElementById('scaleBoard2');
                        if (size.x === 19 && size.y === 19) {
                            scaleBoard1.removeAttribute('disabled');
                            scaleBoard1.checked = true;
                        }else {
                            scaleBoard1.setAttribute('disabled', 'true');
                            scaleBoard2.checked = true;
                        }
                    }
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
