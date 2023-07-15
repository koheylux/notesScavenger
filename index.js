const onClickAdd = () => {
  //テキストボックスの値を取得
  const inputText = document.getElementById("add-text").value;
  const p = document.createElement("p");      //pタグ
  p.innerText = inputText;

  //テキストボックス内を初期化
  document.getElementById("add-text").value = "";

  addIncompleteList(p);
};


//未完了リストに追加する関数
const addIncompleteList = (p) => {
  const li = document.createElement("li");    //liタグ
  const div = document.createElement("div");  //divタグ
  const compliteButton = document.createElement("button");//完了buttonタグ
  const deleteButton = document.createElement("button");  //削除buttonタグ

  //divタグにクラス名付与
  div.className = "list_row";

  compliteButton.innerText = "完了";
  deleteButton.innerText = "削除";

  //完了ボタンの処理
  compliteButton.addEventListener("click", () => {
    deleteButton.parentNode.parentNode.remove();
    addCompleteList(p);
  });

  //削除ボタンの処理
  deleteButton.addEventListener("click", () => {
    deleteButton.parentNode.parentNode.remove();
  });

  div.appendChild(p);               //pをdivにアペンド
  div.appendChild(compliteButton);  //完了ボタンをdivにアペンド
  div.appendChild(deleteButton);    //削除ボタンをdivにアペンド
  li.appendChild(div);              //divをliにアペンド

  document.getElementById("incomplete_list").appendChild(li); //未完了TODOに追加
};


//完了リストに追加する関数
const addCompleteList = (p) => {
  const li = document.createElement("li");    //liタグ
  const div = document.createElement("div");  //divタグ
  const backButton = document.createElement("button");  //戻すbuttonタグ

  //divタグにクラス名付与
  div.className = "list_row";

  backButton.innerText = "戻す";

  //戻すボタンの処理
  backButton.addEventListener("click", () => {
    backButton.parentNode.parentNode.remove();
    addIncompleteList(p);
  })

  div.appendChild(p);               //pをdivにアペンド
  div.appendChild(backButton);      //戻すボタンをdivにアペンド
  li.appendChild(div);              //divをliにアペンド

  document.getElementById("complete_list").appendChild(li); //完了TODOに追加
};

document.getElementById("add-button").addEventListener("click", onClickAdd);