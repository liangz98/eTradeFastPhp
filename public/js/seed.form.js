function SeedMsgBox(){
	if(document.getElementById("SeedMsgTxt")){
		document.body.removeChild(document.getElementById("SeedMsgTxt"));
	}
	var msgObj=document.createElement("div");
	msgObj.setAttribute("id","SeedMsgTxt");
	document.body.appendChild(msgObj);
}

$().ready(function() {
	SeedMsgBox();
});