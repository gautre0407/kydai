//function remove 1 số nút bấm k cần thiết trong tool
function removeElements(selectors, besogoEditor) {
  // Xóa các phần tử theo selector
  selectors.forEach((selector) => {
    document.querySelectorAll(selector).forEach((e) => e.remove());
  });

  // Kiểm tra nếu besogoEditor có hàm click
  if (besogoEditor && typeof besogoEditor.click === "function") {
    const originalClick = besogoEditor.click; // Lưu lại hàm gốc
    besogoEditor.click = function (i, j, ctrlKey, shiftKey) {
      if (ctrlKey || shiftKey) {
        return; // Ngăn chặn Ctrl + Click và Shift + Click
      }
      return originalClick.call(this, i, j, ctrlKey, shiftKey); // Gọi lại hàm gốc
    };
  }
}

//function set nước đi đầu tiên là đen hay trắng
function setFirstMove(requestStatus, numberColor, besogoEditor) {
  if (besogoEditor && typeof besogoEditor.getRoot === "function") {
    const root = besogoEditor.getRoot();
    if (root) {
      root.requestColor = requestStatus;
      root.firstColor = numberColor;
    }
  }
}

//update sgf vào input
function updateSgf(besogoEditor, outputId, besogoInstance) {
  const sgfOutput = document.getElementById(outputId);
  if (!sgfOutput) return; // Kiểm tra nếu không có phần tử thì dừng luôn

  let sgfContent =
    besogoInstance.composeSgf(besogoEditor)?.replace(/\s+/g, " ") || "";
  sgfOutput.value = sgfContent;
}

//gắn sự kiện lắng nghe updateSgf và gọi lần đầu
function bindUpdateSgf(editorBesogo, outputId, besogoInstance) {
  updateSgf(editorBesogo, outputId, besogoInstance); // Gọi lần đầu tiên
  editorBesogo.addListener(() =>
    updateSgf(editorBesogo, outputId, besogoInstance)
  ); // Chỉ gán listener 1 lần
}

//đổi theme bàn cờ
(function () {
  var current = 0,
    themes = "wood flat bold dark book eidogo glift kibitz sensei".split(" "),
    link = document.getElementById("theme");

  window.toggleTheme = function () {
    current = (current + 1) % themes.length;
    link.href =
      window.besogoPublicLink + "/css/board-" + themes[current] + ".css";
  };
})();
