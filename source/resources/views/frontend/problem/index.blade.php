@extends('frontend.layouts.app')

@section('besogo-css')
    <link rel="stylesheet" type="text/css" href="besogo/css/besogo.css">
    <link rel="stylesheet" type="text/css" href="besogo/css/board-wood.css" id="theme">
@endsection

@section('content')

    <div>
        
        <div class="card">
            <div class="card-header">
                Tạo đề bài tập
            </div>
            <div class="card-body">
                <div class="besogo-editor" style="height: 525px; width: 960px; flex-direction: row;" resize="fixed" realstones="on" coord="western"></div>
                
            </div>
        </div>
        <form action="{{ route('problem.store') }}" method="POST">
            @csrf
            <div class="card">
                <div class="card-body">
                    <div class="sgf-box">
                        <label class="mr-3">SGF:</label>
                        <textarea id="sgfOutput" readonly class="form-control" name="question"></textarea>
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
                    sgfContent = sgfContent.replace(/\s+/g, '');
                    document.getElementById("sgfOutput").value = sgfContent;
                }
                editor.addListener(updateSgf); 
                updateSgf();

            } else {
                console.error("Không tìm thấy editor! Kiểm tra class hoặc cách khởi tạo.");
            }

        } else {
            console.error("besogo.autoInit không tồn tại hoặc chưa được tải!");
        } 
    });

</script>

@endsection
