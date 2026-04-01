h2{
	font-family: monospace;
	font-size: 35px;
	font-weight: bold;
	text-align: center;
	color: antiquewhite;

	margin: 0px auto;
	padding-bottom: 25px;
}

body{
	margin: 0px;
    
    background-image: url(imej/wood8.png);
    background-repeat: no-repeat;
    background-size: cover;
    background-attachment: fixed;
}

div {
	text-align: center;
	margin: 0px auto;
	padding-top: 20px;
}

div.menu{
	height:150px;
	background-color: dimgrey;
}

div.peserta{
	width: 520px;
}

div.hakim{
	width: 560px;
}

div.urusetia{
	width: 700px;
}

div.guru{
	width: 560px;
}


ul {
	list-style: none;
	padding: 0px;
	margin: 0px;
	text-align: center;
}

ul li {
	display: block;
	float: left;
}

li ul {
	display: none;
}

ul li a {
	font-family: verdana;
	font-size: 14px;
	font-weight: normal;
	text-align: center;
        color: black;

	width: 95px;
    padding: 9px;
    border: 1px dotted darkblue;
    margin: 0px;
        background-color: gainsboro;
    padding-bottom: 10px;

	display: block;
	text-decoration:none;
}

ul li a:hover {
	color: white;
	background-color: tan;
}

li:hover ul {
	display: block;
	position: absolute;
}

li:hover li {
	float: none;
}

li:hover a {
	color: cadetblue;
	background-color: gainsboro;
}
