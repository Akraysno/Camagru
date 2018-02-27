
var i = 1;

function getXMLHttpRequest() {
	var xhr = null;

	if (window.XMLHttpRequest || window.ActiveXObject) {
		if (window.ActiveXObject) {
			try {
				xhr = new ActiveXObject("Msxml2.XMLHTTP");
			} catch(e) {
				xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
		} else {
			xhr = new XMLHttpRequest(); 
		}
	} else {
		alert("Votre navigateur ne supporte pas l'objet XMLHTTPRequest...");
		return null;
	}

	return xhr;
}
function getScrollXY() {
	var scrOfX = 0, scrOfY = 0;
	if( typeof( window.pageYOffset ) == 'number' ) {
		//Netscape compliant
		scrOfY = window.pageYOffset;
		scrOfX = window.pageXOffset;
	} else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) {
		scrOfY = document.body.scrollTop;
		scrOfX = document.body.scrollLeft;
	} else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) {
		//IE6 standards compliant mode
		scrOfY = document.documentElement.scrollTop;
		scrOfX = document.documentElement.scrollLeft;
	}
	return new Array(scrOfX, scrOfY);
}
function create_calque(index, name, src, pos_x, pos_y) {
	if (name === "ERROR")
		return ;
	var calque = document.createElement('img'),
		new_id = "calque" + index;
	calque.src = src;
	calque.id = new_id;
	calque.style.top = pos_y + "px";
	calque.style.left = pos_x + "px";
	calque.style.position = "absolute";
	calque.style.zIndex = index;
	return (calque);
}
function create_elem(index) {
	var elem = document.createElement('div');
	elem.id = "elem" + index;
	elem.zIndex = index + 1;
	elem.style = "border: 1px solid #929292; width: 200px; height: 70px; background-color: #e7c975; margin: 5px 5px 5px 5px;";
	elem.onmouseover = function () {
		/([0-9]+)/.exec(this.id);
		var nb = RegExp.$1;
		document.getElementById("calque" + nb).style.filter = "saturate(500%)";
		document.getElementById("calque" + nb).style.zIndex = i + 1;
	};
	elem.onmouseout = function () {
		/([0-9]+)/.exec(this.id);
		var nb = RegExp.$1;
		document.getElementById("calque" + nb).style.filter = "initial";
		document.getElementById("calque" + nb).style.zIndex = elem.zIndex;
	};
	return (elem);
}
function add_cross_elem(index) {
	var cross = document.createElement('img');
	cross.name = "cross" + index;
	cross.src = "images/cross.png";
	cross.onclick = function () {
		/([0-9]+)/.exec(this.name);
		var nb = RegExp.$1;
		document.getElementById("calque"+nb).remove();
		document.getElementById("elem"+nb).remove();
		var xhr = getXMLHttpRequest();
		xhr.open("POST", "includes/add_calque.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) 
			{ 
				console.log("suppression termine");
				console.log(xhr.responseText);
				document.getElementById('verif_test').innerHTML = xhr.responseText;
			}
		}
		xhr.send("action=del&i="+nb);
	};
	cross.id = "info_cross";
	return (cross);
}
function add_title_elem(index, name) {
	var title = document.createElement('p');
	var titre = index + " - " + name;
	title.id = "info_title";
	title.innerHTML = titre;
	return (title);
}
function add_info_elem(width, height) {
	var info = document.createElement('p');
	var text = "(Image size: width: " + width + "px, height: " + height + "px)"
	info.id = "info_dim";
	info.innerHTML = text;
	return (info);
}
function add_title_pos_elem() {
	var title_pos = document.createElement('p');
	title_pos.id = "info_pos";
	title_pos.innerHTML = "Position:";
	return (title_pos);
}
function add_title_pos_x_elem() {
	var title_pos_x = document.createElement('p');
	title_pos_x.id = "info_x";
	title_pos_x.innerHTML = "x:";
	return (title_pos_x);
}
function add_title_pos_y_elem() {
	var title_pos_y = document.createElement('p');
	title_pos_y.id = "info_y";
	title_pos_y.innerHTML = "y:";
	return (title_pos_y);
}
function add_input_x_elem(index, pos_x) {
	var input_pos_x = document.createElement('input');
	input_pos_x.id = "info_in_x";
	input_pos_x.name = "info_in_x"+index;
	input_pos_x.type = "number";
	input_pos_x.value = pos_x;
	input_pos_x.onkeyup = function () {
		/([0-9]+)/.exec(this.name);
		var nb = RegExp.$1;
		document.getElementById("calque"+nb).style.left = this.value + "px";
		var xhr = getXMLHttpRequest();
		xhr.open("POST", "includes/add_calque.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) { 
				console.log("modif termine");
				console.log(xhr.responseText);
				document.getElementById('verif_test').innerHTML = xhr.responseText;
			}
		}
		xhr.send("action=mod&i="+nb+"&field=pos_x&value="+this.value);
	}
	return (input_pos_x);
}
function add_input_y_elem(index, pos_y) {
	var input_pos_y = document.createElement('input');
	input_pos_y.id = "info_in_y";
	input_pos_y.name = "info_in_y"+index;
	input_pos_y.type = "number";
	input_pos_y.value =pos_y;
	input_pos_y.onkeyup = function () {
		/([0-9]+)/.exec(this.name);
		var nb = RegExp.$1;
		document.getElementById("calque"+nb).style.top = this.value + "px";
		var xhr = getXMLHttpRequest();
		xhr.open("POST", "includes/add_calque.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) 
			{ 
				console.log("modif termine");
				console.log(xhr.responseText);
				document.getElementById('verif_test').innerHTML = xhr.responseText;
			}
		}
		xhr.send("action=mod&i="+nb+"&field=pos_y&value="+this.value);
	}
	return (input_pos_y);
}
function create_element(index, name, width, height, pos_x, pos_y) {
	var elem = create_elem(index);
	var cross = add_cross_elem(index);
	var title = add_title_elem(index, name);
	var info = add_info_elem(width, height);
	var title_pos = add_title_pos_elem();
	var title_pos_x = add_title_pos_x_elem();
	var input_pos_x = add_input_x_elem(index, pos_x);
	var title_pos_y = add_title_pos_y_elem();
	var input_pos_y = add_input_y_elem(index, pos_y);

	elem.appendChild(title);
	elem.appendChild(cross);
	elem.appendChild(info);
	elem.appendChild(title_pos);
	elem.appendChild(title_pos_x);
	elem.appendChild(input_pos_x);
	elem.appendChild(title_pos_y);
	elem.appendChild(input_pos_y);
	return (elem);
}
function add_calque_and_elem(index, data, action) {
	var calque = create_calque(index, data["name"], data["src"], data["pos_x"], data["pos_y"]);
	if (!calque)
		return ;
	document.getElementById("allCalques").insertBefore(calque, document.getElementById("allCalques").firstChild);
	var elem = create_element(index, data["name"], data["width"], data["height"], data["pos_x"], data["pos_y"]);
	document.getElementById("elements").appendChild(elem);
	document.getElementById("elements").scrollTop = document.getElementById("elements").scrollHeight;
	if (action === 1) {
		var xhr = getXMLHttpRequest();
		xhr.open("POST", "includes/add_calque.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) 
			{ 
				console.log("Ajout termine");
				console.log(xhr.responseText);
				//document.getElementById('verif_test').innerHTML = xhr.responseText;
			}
		}
		xhr.send("action=add&i="+index+"&name="+data["name"]+"&pos_x="+data["pos_x"]+"&pos_y="+data["pos_y"]+"&width="+data["width"]+"&height="+data["height"]+"&src="+data["src"]);
	}
}
function modif_checked(name) {
	if (document.getElementById(name).checked) {
		console.log(name);
		var xhr = getXMLHttpRequest();
		xhr.open("POST", "includes/radio_checked.php", true);
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function() {
			if(xhr.readyState == 4 && xhr.status == 200) 
			{ 
				console.log("check changed");
				console.log(xhr.responseText);
			}
		}
		xhr.send("new_check="+name);
	}
}