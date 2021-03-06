/* This notice must be untouched at all times.

FreeGroup Draw2D 0.9.26
The latest version is available at
http://www.freegroup.de

Copyright (c) 2006 Andreas Herz. All rights reserved.
Created 5. 11. 2006 by Andreas Herz (Web: http://www.freegroup.de )

LICENSE: LGPL

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License (LGPL) as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA,
or see http://www.gnu.org/copyleft/lesser.html
*/

/**
 * Base class for the undo redo support in the FreeGroup Draw2D 0.9.26 framework.
 *
 * @version 0.9.26
 * @author Andreas Herz
 * @constructor
 */
draw2d.Command=function(/*:String*/ label)
{
  this.label = label;
};

/** @private **/
draw2d.Command.prototype.type="draw2d.Command";

/**
 * Returns a label of the Command. e.g. "move figure".
 *
 * @final
 **/
draw2d.Command.prototype.getLabel=function()
{
   return this.label;
};


/**
 * Returns [true] if the command can be execute and the execution of the
 * command modifies the model. e.g.: a CommandMove with [startX,startX] == [endX,endY] should
 * return false. Rhe execution of this Command doesn't modify the model.
 *
 * @type boolean
 **/
draw2d.Command.prototype.canExecute=function()
{
  return true;
};

/**
 * Execute the command the first time.
 * Sup-classes must implement this method.
 **/
draw2d.Command.prototype.execute=function()
{
};

/**
 * Will be called if the user cancel the operation.
 *
 * @since 0.9.15
 **/
draw2d.Command.prototype.cancel=function()
{
};

/**
 * Undo the command.
 * Sup-classes must implement this method.
 *
 **/
draw2d.Command.prototype.undo=function()
{
};

/** 
 * Redo the command after the user has undo this command.
 * Sup-classes must implement this method.
 *
 **/
draw2d.Command.prototype.redo=function()
{
};
