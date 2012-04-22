<!doctype html>
<html>
	<head>
		<title>Anontune - <%?title></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href='http://fonts.googleapis.com/css?family=Paytone+One|Cantarell:400,700|PT+Sans+Caption:400,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="/css/kickstart.css">
		<link rel="stylesheet" type="text/css" href="/css/style.css?1">
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script type="text/javascript" src="/js/prettify.js"></script>
		<script type="text/javascript" src="/js/kickstart.js?3"></script>
	</head>
	<body>
		<div class="wrapper top">
			<div class="locale">
				<a href="/setlocale/english/">English</a> &nbsp;
				<a href="/setlocale/danish/">Dansk</a> &nbsp;
				<a href="/setlocale/dutch/">Nederlands</a>
			</div>
			<h1>Anontune <sup><%!main-sup></sup></h1>
		</div>
		<div class="menu-style">
			<div class="wrapper">
				<ul class="menu no-style-menu">
					<li <%?set-home>><a href="/">Home</a></li>
					<li <%?set-demo>><a href="/demo/">
						<span class="icon small" data-icon="A"></span>
						<%!main-button-demo>
					</a></li>
					<li <%?set-login>><a href="/login/">
						<span class="icon small" data-icon="O"></span>
						<%!main-button-login>
					</a></li>
					<li <%?set-register>><a href="/register/">
						<span class="icon small" data-icon="7"></span>
						<%!main-button-register>
					</a></li>
					<li <%?set-about>>
						<a href="/about/">
							<span class="icon small" data-icon="i"></span>
							<%!main-button-about>
						</a>
						<ul>
							<%?menu-about>
						</ul>
					</li>
					<li <%?set-tools>>
						<a href="/tools/">
							<span class="icon small" data-icon="Z"></span>
							<%!main-button-tools>
						</a>
						<ul>
							<%?menu-tools>
						</ul>
					</li>
					<li <%?set-contribute>><a href="/contribute/">
						<span class="icon small" data-icon="h"></span>
						<%!main-button-contribute>
					</a></li>
					<li <%?set-irc>><a href="/irc/" class="highlighted">
						<span class="icon small" data-icon='"'></span>
						<%!main-button-irc>
					</a></li>
				</ul>
			</div>
		</div>
		<div class="ribbon">
			<div class="wrapper">
				<%?contents>
			</div>
		</div>
		<div class="wrapper">
			
		</div>
	</body>
</html>
