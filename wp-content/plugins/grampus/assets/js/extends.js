jQuery(function($) {
  var origSetTimeout;
  var email_regex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

  $.fn.exists = function()
  {
    return this.length > 0;
  };

  _validate_email = function (input) {
    if (input.value.match(email_regex))
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  $.fn.eserialize = function(selector,debug)
  {
    var all, error_obj, formData;
    if (debug == undefined)
    {
      debug = false;
    }
    if (selector == null)
    {
      selector = '';
    }
    formData = new FormData();
    error_obj = [];
    all = true;
    delayed = {};
    if (selector === '')
    {
      selector = 'input, textarea, select, button[type="submit"]';
    }
    this.find(selector).each(function(index, input)
    {
      if(!input.name)
      {
        return true;
      }
      var type = input.tagName.toLowerCase();
      if (type == 'select')
      {
        if (input.hasAttribute('multiple'))
        {
          var sel_values = [];
          var options = input.options;
          var opt;

          for (var i = 0, iLen = options.length; i < iLen; i++)
          {
            opt = options[i];

            if (opt.selected)
            {
              sel_values.push(opt.value || opt.text);
            }
          }

          if (sel_values.length>0)
          {
            for (sv in sel_values)
            {
              formData.append(input.name+'[]', sel_values[sv]);
            }
            return true;
          }
          else
          {
            if (input.getAttribute('required') !== null)
            {
              error_obj.push(input);
              all = false;
            }
            else
            {
              formData.append(input.name, input.value);
            }
            return true;
          }
        }
        else
        {
          if (input.value !== '')
          {
            formData.append(input.name, input.value);
          }
          else
          {
            if (input.getAttribute('required') !== null)
            {
              error_obj.push(input);
              all = false;
            }
            else
            {
              formData.append(input.name, input.value);
            }
            return true;
          }
        }
      }
      else
      {
        if (input.value !== '')
        {
          if (input.getAttribute('type') === 'tel')
          {
            if ('formPhoneValidator' in window)
            {
              var valid = window.formPhoneValidator(input.value);
              if (valid)
              {
                formData.append(input.name, input.value);
              }
              else
              {
                error_obj.push(input);
                all = false;
              }
            }
            else
            {
              formData.append(input.name, input.value);
            }
          }
          else if (input.getAttribute('type') === 'email')
          {
            var valid = _validate_email(input);
            if (valid)
            {
              formData.append(input.name, input.value);
            }
            else
            {
              error_obj.push(input);
              all = false;
            }
          }
          else if (input.getAttribute('type') === 'file')
          {
            for (var i = input.files.length - 1; i >= 0; i--)
            {
              formData.append(input.name, input.files[i]);
            }
          }
          else if (input.getAttribute('type') === 'checkbox')
          {
            if (input.indeterminate === true)
            {
              if (input.getAttribute('value-indeterminate'))
              {
                formData.append(input.name, input.getAttribute('value-indeterminate'));
              }
            }
            else if (input.checked === true)
            {
              formData.append(input.name, input.value);
            }
            else
            {
              if (input.getAttribute('required') !== null)
              {
                error_obj.push(input);
                all = false;
              }
              else if (input.getAttribute('value-unchecked'))
              {
                formData.append(input.name, input.getAttribute('value-unchecked'));
              }
            }
          }
          else if (input.getAttribute('type') === 'radio')
          {
            if(input.checked === true)
            {
              formData.append(input.name, input.value);
            }
            else
            {
              if(input.getAttribute('required') !== null && !formData.get(input.name))
              {
                if(!(input.name in delayed))
                {
                  delayed[input.name] = [];
                }
                delayed[input.name].push(input);
                // error_obj.push(input);
                // all = false;
              }
            }
          }
          else
          {
            formData.append(input.name, input.value);
          }
          return true;
        }
        else
        {
          if (input.getAttribute('required') !== null)
          {
            error_obj.push(input);
            all = false;
          }
          else
          {
            if (input.getAttribute('type') !== 'checkbox' && input.getAttribute('type') !== 'radio')
            {
              formData.append(input.name, input.value);
            }
          }
          return true;
        }
      }
    });
    if(Object.keys(delayed).length>0)
    {
      for(name in delayed)
      {
        if(!formData.get(name))
        {
          for(input in delayed[name])
          {
            error_obj.push(delayed[name][input]);
          }
          all = false;
        }
      }
    }
    if(debug)
    {
      for (var pair of formData.entries())
      {
        console.log(pair[0]+ ' => '+ pair[1]);
      }
    }
    return {
      status: all,
      data: formData,
      error: error_obj
    };
  };

  window.TOtriggers = [];

  origSetTimeout = window.setTimeout;

  window.setTimeout = function(callback, timeout, mark)
  {
    if (mark == null)
    {
      mark = null;
    }
    if (mark)
    {
      return window.TOtriggers[mark] = origSetTimeout(callback, timeout);
    }
    else
    {
      return origSetTimeout(callback, timeout);
    }
  };

  window.UUID = function()
  {
    var dt, uuid;
    dt = new Date().getTime();
    uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c)
    {
      var r;
      r = (dt + Math.random() * 16) % 16 | 0;
      dt = Math.floor(dt / 16);
      return (c === 'x' ? r : r & 0x3 | 0x8).toString(16);
    });
    return uuid;
  };
});