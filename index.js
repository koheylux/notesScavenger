const onClickAdd = () => {
  //テキストボックスの値を取得　&　初期化
  const inputText = document.getElementById("add-text").value;
  document.getElementById("add-text").value = "";

  //liタグの生成
  const li = document.createElement("li");


  //divタグの中身生成
  const div = document.createElement("div");
  div.className = "list-row";

  const p = document.createElement("p"); //pタグの生成
  const compliteButton = document.createElement("button"); //完了buttonタグの生成
  const deleteButton = document.createElement("button"); //削除buttonタグの生成

  p.innerText = inputText; //pに入力文字を格納
  compliteButton.innerText = "完了";
  deleteButton.innerText = "削除";

  compliteButton.addEventListener("click", () => {
    
  });

  deleteButton.addEventListener("click", () => {
    const deleteTarget = deleteButton.parentNode.parentNode;
    document.getElementById("incomplete-list").removeChild(deleteTarget);
  });

  div.appendChild(p); //pをdivにアペンド
  div.appendChild(compliteButton); //完了ボタンをdivにアペンド
  div.appendChild(deleteButton); //削除ボタンをdivにアペンド

  //liタグの子要素にdivを設定
  li.appendChild(div);

  //未完了リストに追加
  document.getElementById("incomplete-list").appendChild(li);
};


document.getElementById("add-button").addEventListener("click", onClickAdd);