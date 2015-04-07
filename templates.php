<?php
/**
 *	This file contains some templates for the result.php file
 *
 */
define("PARTICIPANT_TEMPLATE", '<div class="champlloadin" id=participant{participantId}>
                        <div class="champpic">
                            <span class="level" data-uk-tooltip title="Level">{level}</span>
                            <img src="images/champion/{champname}.png" width="79" height="79" alt="{champname}" />
                            <div class="summonerspell">
                                <img src="images/summoners/Summoner{sum1}.png" data-uk-tooltip title="{sum1}" width="37" height="37" alt="{sum1}" />
                                <img src="images/summoners/Summoner{sum2}.png" data-uk-tooltip title="{sum2}" width="37" height="37" alt="{sum2}" />
                            </div>                                
                        </div> 
                        <div class="summonerkda">
                            {champname} <br />
                            <span class="kda" data-uk-tooltip title="Kill - Death - Assist">{kills} - {deaths} - {assists}</span>
                        </div>       
                        <div class="champbuild">
                            <table>
                                <tr>
                                    <td><div><span style="display:none" title="Stock" data-cached-title="" class="stat" data-uk-tooltip="">2</span><img src="images/items/{item0}.png" data-uk-tooltip title="{item0}" width="38" height="38" alt="{item0}" /><div></td>
                                    <td><div><span style="display:none" title="Stock" data-cached-title="Stat" class="stat" data-uk-tooltip="">2</span><img src="images/items/{item1}.png" data-uk-tooltip title="{item1}" width="38" height="38" alt="{item1}" /><div></td>
                                    <td><div><span style="display:none" title="Stock" data-cached-title="Stat" class="stat" data-uk-tooltip="">2</span><img src="images/items/{item2}.png" data-uk-tooltip title="{item2}" width="38" height="38" alt="{item2}" /><div></td>
                                </tr>
                                <tr>
                                    <td><div><span style="display:none" title="Stock" class="stat" data-uk-tooltip="">2</span><img src="images/items/{item3}.png" data-uk-tooltip title="{item3}" width="38" height="38" alt="{item3}" /><div></td>
                                    <td><div><span style="display:none" title="Stock" class="stat" data-uk-tooltip="">2</span><img src="images/items/{item4}.png" data-uk-tooltip title="{item4}" width="38" height="38" alt="{item4}" /><div></td>
                                    <td><div><span style="display:none" title="Stock" class="stat" data-uk-tooltip="">2</span><img src="images/items/{item5}.png" data-uk-tooltip title="{item5}" width="38" height="38" alt="{item5}" /><div></td>
                                    <td class="trinket" colspan="3"><img src="images/items/{trinket}.png" data-uk-tooltip title="{trinket}" width="38" height="38" alt="{trinket}" /></td>
                                </tr>
                                <tr>
                                </tr>                              
                            </table>
                        </div>
                        <div class="goldminions">
                            <div class="gold">Gold</div>
                            <div id="currentGold" class="countgold">{gold}</div>    
                        </div>
                        <div class="minion">
                            <div class="gold">Minions</div>
                            <div id="currentMinions" class="countgold">{minions}</div>    
                        </div>                        
                    </div>  ');

?>