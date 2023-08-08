<?
	require_once "functions_knj_extensions.php";
	knj_dl("gtk2");
	Gtk::rc_parse("gtkrc");
	
	class WinMain{
		function __construct(){
			$this->window = new GladeXML("glades/win_main.glade");
			
			$this->store = new GtkListStore(Gtk::TYPE_STRING);
			
			$this->window->signal_autoconnect_instance($this);
			$this->window->get_widget("window")->show_all();
			
			$this->window->get_widget("tv_files")->set_model($this->store);
			$this->window->get_widget("tv_files")->append_column(new GtkTreeViewColumn("Fil", new GtkCellRendererText(), "text", 0));
			
			//opdaterer fil-listen.
			$this->DirChoose();
		}
		
		function CloseWindow(){
			echo "Goodbye.\n";
			Gtk::main_quit();
		}
		
		function DirChoose(){
			$this->store->clear();
			$folder = $this->window->get_widget("fc_dir")->get_current_folder();
			
			$fp = opendir($folder);
			while(($file = readdir($fp)) !== false){
				if ($file != "." && $file != ".." && !is_dir($folder . "/" . $file)){
					$this->store->append(array($file));
				}
			}
		}
		
		function ReplaceClicked(){
			//henter replace-text.
			$replace_from = $this->window->get_widget("tex_replace_from")->get_text();
			$replace_to = $this->window->get_widget("tex_replace_to")->get_text();
			
			//clearer list-store og henter current-folder.
			$this->store->clear();
			$folder = $this->window->get_widget("fc_dir")->get_current_folder();
			
			//replacer file-names og tilføjer nye navne til listen.
			$fp = opendir($folder);
			while(($file = readdir($fp)) !== false){
				if ($file != "." && $file != ".."){
					//nyt navn.
					$file_new = str_replace($replace_from, $replace_to, $file);
					
					//rename fil og tilføj til liste.
					$renames[$file] = $file_new;
				}
			}
			
			foreach($renames AS $key => $value){
				if (rename($folder . "/" . $key, $folder . "/" . $value)){
					$this->store->append(array($value));
				}else{
					$this->store->append(array($key));
				}
			}
		}
	}
	
	$win_main = new WinMain();
	Gtk::main();
?>