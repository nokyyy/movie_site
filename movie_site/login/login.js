//loginpageのJS
function deleteCheck() {
  if(window.confirm(/*i+"を*/"削除してもよろしいですか？")){
    return true;
  } else {
    return false;
  }
}

//初期表示は非表示
//document.getElementById("userTable").style.display ="none";
function clickDisplay(){
	const ut = document.getElementById("userTable");

	if(ut.style.display=="block"){
		// noneで非表示
		ut.style.display ="none";
	}else{
		// blockで表示
		ut.style.display ="block";
	}
}
