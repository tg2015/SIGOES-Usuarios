

1. Quitar el codigo de cifrado que AAM pone al nombre del Rol: 
	--->linea 23:
		Archivo advanced-access-manager/application/view/role.php 

2. Ocultar notificaciones de actualizacion, en ventana del plugin:
	--->linea 10 a 24
	--->linea 78 a 91
		Archivo advanced-access-manager/application/view/tmpl/manager.phtml

3. Poner menus en espanol y ocultar submenus que contienen propaganda:
	--->linea 936
		Archivo aam.php (Index)		
	--->linea 335
		Archivo extension/AAM_Secure/extension.php

4. Titulo de pagina principal: SIGOES-Administracion Rol-Usuario
	--->linea 10
		Archivo application/view/tmpl/manager.phtml

5. Ocultar visitors en control manager 
	---> linea 94
		Archivo application/view/manager.php
	--->linea 133
		Archivo aplication/view/manager.php

6. botones español roleback y save (restaurar y guardar (Panel de control))		
	--->linea 284
		Archivo media/js/aam.js

7. menu capacidades en español 
	---> liena 102
		Archivo application/view/capability.php