// Создаём плагин
CKEDITOR.plugins.add( 'nagupload',
{
	init: function( editor )
	{
		// Создаём кнопку на панели инструментов, и назначаем для неё команду - показать диалоговое окно
		editor.addCommand( 'naguploadimage', new CKEDITOR.dialogCommand( 'naguploadimage' ) );
		// Подключаем дополнительный js-файл, в котором будут описаны команды по показу диалогового окно
		CKEDITOR.dialog.add( 'naguploadimage', this.path + 'dialogs/upload.js' );
		// Название кнопки на панели инструментов
		editor.ui.addButton( 'NagUploadImage',
			{
				// Название кнопки
				label: 'Загрузка изображения' ,
                // Команда для вызова
                command: 'naguploadimage',
                icon: this.path + 'icons/nagupload.png',
				toolbar: 'insert,0'
			}
		);
	}
} );

