import tinymce from 'tinymce/tinymce';
import 'tinymce/icons/default';
import 'tinymce/themes/silver';
import 'tinymce/models/dom';
import 'tinymce/plugins/link';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/autoresize';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/skins/content/default/content.min.css';

function initWysiwyg() {
	const selector = 'textarea.wysiwyg';
	if (!document.querySelector(selector)) return;
	try {
		// Remove any previous editors to avoid duplicates on hot-reload
		if (tinymce?.remove) tinymce.remove();
		tinymce.init({
			selector,
			plugins: 'link lists table autoresize',
			toolbar: 'undo redo | styles | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table',
			menubar: false,
			min_height: 600,
			// Use bundled styles rather than loading from relative URLs
			skin: false,
			content_css: false,
			content_style: 'body{font-family:Inter,system-ui,sans-serif;font-size:14px} table{border-collapse:collapse;width:100%} table,th,td{border:1px solid #e5e7eb;} th,td{padding:6px;}'
		});
	} catch (e) {
		console.error('TinyMCE init failed:', e);
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initWysiwyg);
} else {
	initWysiwyg();
}

// Expose manual re-init hook if needed
window.initWysiwyg = initWysiwyg;


