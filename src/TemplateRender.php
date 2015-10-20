<?php
class TemplateRender
{
	/**
	 * @method méthode permettant de renvoyer un
	 * template donné.
	 * @param string $name : le du template à renvoyer
	 * @param mixed $data la donnée afficher dans le template
	 */
    public static function render($name,$res)
	{
		ob_start();
		require_once($name);
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
	/**
	 * @method getter renvoyant le nom du template
	 * @return string
	 */
	public  function getName()
	{
		return $this->name;
	}
	/**
	 * setteur permettant de modifier le nom du template
	 * @param string $name
	 */
	public  function setName($name)
	{
		return $this->name = $name;
	}
}
