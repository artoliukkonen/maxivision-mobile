<?
function dom_to_array($root) {
  // sweisman at pobox dot com 24-Sep-2009 01:19 @ http://php.net/manual/en/book.dom.php
    $result = array();

    if ($root->hasAttributes())
    {
        $attrs = $root->attributes;

        foreach ($attrs as $i => $attr)
            $result[$attr->name] = $attr->value;
    }

    $children = $root->childNodes;

    if (is_object($children) && $children->length == 1) {
        $child = $children->item(0);

        if ($child->nodeType == XML_TEXT_NODE)
        {
            $result['_value'] = $child->nodeValue;

            if (count($result) == 1)
                return $result['_value'];
            else
                return $result;
        }
    }

    $group = array();
    
    if(is_object($children)) {
      for($i = 0; $i < $children->length; $i++) {
          $child = $children->item($i);

          if (!isset($result[$child->nodeName]))
              $result[$child->nodeName] = dom_to_array($child);
          else
          {
              if (!isset($group[$child->nodeName]))
              {
                  $tmp = $result[$child->nodeName];
                  $result[$child->nodeName] = array($tmp);
                  $group[$child->nodeName] = 1;
              }

              $result[$child->nodeName][] = dom_to_array($child);
          }
      }
    }

    return $result;
}