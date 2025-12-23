<?php
/* Copyright (C) 2025 ATM Consulting
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

require_once __DIR__ . '/../backport/v19/core/class/commonhookactions.class.php';

/**
 * Class ActionsSearcheverywhere
 */
class ActionsSearcheverywhere extends \searcheverywhere\RetroCompatCommonHookActions
{
	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param array        $parameters   Meta datas of the hook (context, etc...)
	 * @param CommonObject $object       The object you want to process (invoice, propal, etc.)
	 * @param string       $action       Current action (if set). Generally create or edit or null
	 * @param HookManager  $hookmanager  Hook manager instance
	 *
	 * @return int
	 */
	public function printSearchForm($parameters, &$object, &$action, $hookmanager)
	{
		global $langs,$db,$conf;

		if (in_array('searchform', explode(':', $parameters['context']))) {
			$langs->load('searcheverywhere@searcheverywhere');

			$res = '';
			$newToken = function_exists('newToken') ? newToken() : $_SESSION['newtoken'];
			$res.='<form method="post" action="'.dol_buildpath('/searcheverywhere/search.php', 1).'">';
			$res.= '<input type="hidden" name="token" value="'.$newToken.'">';
			$res.= '<div class="menu_titre menu_titre_search"><label for="sew_keyword"><a class="vsmenu" href="'.dol_buildpath('/searcheverywhere/search.php', 1).'?token='.$newToken.'">'.img_object($langs->trans('searcheverywhere'), 'searcheverywhere@searcheverywhere').' '.$langs->trans('Searcheverywhere').'</a></label></div>';
			$res.= '	<input type="text" size="10" name="keyword" title="'.$langs->trans('Keyword').'" class="flat" id="sew_keyword" /><input type="submit" value="'.$langs->trans('Go').'" class="button">
				</form>';

			if (getDolGlobalString('SEARCHEVERYWHERE_SEARCH_PREVIEW') ) {
				$res.= '<script type="text/javascript">
                $("#sew_keyword").autocomplete({
					      source: function( request, response ) {
					        $.ajax({
					          url: "'.dol_buildpath('/searcheverywhere/script/interface.php', 1).'",
					          dataType: "json",
					          data: {
					            keyword: request.term
					            ,get:"search-all"
					          }
					          ,success: function( data ) {
					          	  var c = [];
					              $.each(data, function (i, cat) {

					              	var first = true;
					              	$.each(cat, function(j, obj) {

					              		if(first) {
					              			c.push({value:i, label:i, object:"title"});
					              			first = false;
					              		}

					              		c.push({ value: j, label:"  "+obj.label_clean, url:obj.url, desc:"  "+obj.desc, object:i});

					              	});


					              });

					              response(c);
					          }
					        });
					      },
					      minLength: 1,
					      select: function( event, ui ) {

                                if(ui.item.url) {
                                    document.location.href = ui.item.url;
                                }

                                return false;

					      },
					      open: function( event, ui ) {
					        $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
					      },
					      close: function() {
					        $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
					      }
			     });

		 		$( "#sew_keyword" ).autocomplete( "instance" )._renderItem = function( ul, item ) {

					      	  $li = $( "<li style=\"white-space: nowrap;\" />" )
								    .attr( "data-value", item.value )
                                    .append("<span class=\"select2-results\" >"+item.label+"</span>" )
								    .appendTo( ul );

							  if(item.object=="title") $li.css("font-weight","bold");

							  return $li;
			     };

                </script>
              ';
			}

			$this->resprints = $res;
		}

		return 0;
	}

	/**
	 * Add "Search everywhere" entry in global search results
	 *
	 * @param array        $parameters   Meta datas of the hook (context, etc...)
	 * @param CommonObject $object       The object you want to process (invoice, propal, etc.)
	 * @param string       $action       Current action (if set). Generally create or edit or null
	 * @param HookManager  $hookmanager  Hook manager instance
	 *
	 * @return int
	 */
	public function addSearchEntry($parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $db, $conf;

		if (in_array('searchform', explode(':', $parameters['context']))) {
			$search_boxvalue = $parameters['search_all'] ?? '';

			$langs->load('searcheverywhere@searcheverywhere');

			dol_include_once('/searcheverywhere/core/modules/modsearcheverywhere.class.php');
			$modSearch = new modsearcheverywhere($db);

			$this->results = array(
				'searchintosearcheverywhere' => array(
					'position' => 600,
					'img'      => floatval(DOL_VERSION) >= 18 ? 'fontawesome_search_fas' : 'object_searcheverywhere',
					'label'    => $langs->trans('Searcheverywhere'),
					'text'     => img_picto('', floatval(DOL_VERSION) >= 18 ? 'fontawesome_search_fas' : 'object_searcheverywhere@searcheverywhere') . ' ' . $langs->trans('Searcheverywhere'),
					'url'      => dol_buildpath('/searcheverywhere/search.php', 1).'?keyword='.urlencode($search_boxvalue),
				)
			);
		}

		return 0;
	}
}
