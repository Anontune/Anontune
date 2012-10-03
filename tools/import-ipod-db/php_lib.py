import re
def url_escape(s, action):
    i = 0
    s_len = len(s)
    new = ""
    while i < s_len:
        #Encode.
        if action:
            # "7-bit ASCII alphanumerics and the characters "-._~" do not need to be escaped."
            if re.match("[-a-zA-Z0-9._~]", s[i]) != None:
                new += s[i]
            else:
                my_ord = ord(s[i])
                hex_ord = "%X" % my_ord
                new += "%" + str(hex_ord)
            i += 1
        #Decode.
        else:
            #Prevent overflow.
            if i + 2 < s_len:
                encoded = s[i:i + 3]
                if re.match("%[0-9a-fA-F][0-9a-fA-F]", encoded) != None:
                    encoded = encoded[1:]
                    encoded = int(encoded, 16)
                    new += chr(encoded)
                    i += 3
                    continue
            new += s[i]
            i += 1
                
    return new
    
def htmlspecialchars(s, action):
    new = ""
    specialchars = {34 : r"&quot;",
    38 : r"&amp;",
    39 : r"&#39;",
    60 : r"&lt;",
    62 : r"&gt;",
    94 : r"&circ;",
    126 : r"&tilde;",
    127 : r"&#127;",
    128 : r"&euro;",
    130 : r"&sbquo;",
    131 : r"&fnof;",
    132 : r"&bdquo;",
    133 : r"&hellip;",
    134 : r"&dagger;",
    135 : r"&Dagger;",
    136 : r"&circ;",
    137 : r"&permil;",
    138 : r"&Scaron;",
    139 : r"&lsaquo;",
    140 : r"&OElig;",
    141 : r"&#356;",
    142 : r"&#381;",
    145 : r"&lsquo;",
    146 : r"&rsquo;",
    147 : r"&ldquo;",
    148 : r"&rdquo;",
    149 : r"&bull;",
    150 : r"&ndash;",
    151 : r"&mdash;",
    152 : r"&tilde;",
    153 : r"&trade;",
    154 : r"&scaron;",
    155 : r"&rsaquo;",
    156 : r"&oelig;",
    157 : r"&#357;",
    158 : r"&#382;",
    159 : r"&Yuml;",
    160 : r"&nbsp;",
    161 : r"&#711;",
    162 : r"&#728;",
    163 : r"&#321;",
    164 : r"&curren;",
    165 : r"&#260;",
    166 : r"&brvbar;",
    167 : r"&sect;",
    168 : r"&uml;",
    169 : r"&copy;",
    170 : r"&#350;",
    171 : r"&laquo;",
    172 : r"&not;",
    173 : r"&shy;",
    174 : r"&reg;",
    175 : r"&#379;",
    176 : r"&deg;",
    177 : r"&plusmn;",
    178 : r"&sup2;",
    179 : r"&#322;",
    180 : r"&acute;",
    181 : r"&micro;",
    182 : r"&para;",
    183 : r"&middot;",
    184 : r"&cedil;",
    185 : r"&#261;",
    186 : r"&#351;",
    187 : r"&raquo;",
    188 : r"&#317;",
    189 : r"&#733;",
    190 : r"&#318;",
    191 : r"&#380;",
    192 : r"&#340;",
    193 : r"&Aacute;",
    194 : r"&Acirc;",
    195 : r"&#258;",
    196 : r"&Auml;",
    197 : r"&#313;",
    198 : r"&#262;",
    199 : r"&Ccedil;",
    200 : r"&#268;",
    201 : r"&Eacute;",
    202 : r"&#280;",
    203 : r"&Euml;",
    204 : r"&#282;",
    205 : r"&Iacute;",
    206 : r"&Icirc;",
    207 : r"&#270;",
    208 : r"&#272;",
    209 : r"&#323;",
    210 : r"&#327;",
    211 : r"&Oacute;",
    212 : r"&Ocirc;",
    213 : r"&#336;",
    214 : r"&Ouml;",
    215 : r"&times;",
    216 : r"&#344;",
    217 : r"&#366;",
    218 : r"&Uacute;",
    219 : r"&#368;",
    220 : r"&Uuml;",
    221 : r"&Yacute;",
    222 : r"&#354;",
    223 : r"&szlig;",
    224 : r"&agrave;",
    225 : r"&aacute;",
    226 : r"&acirc;",
    227 : r"&#259;",
    228 : r"&auml;",
    229 : r"&#314;",
    230 : r"&#263;",
    231 : r"&ccedil;",
    232 : r"&#269;",
    233 : r"&eacute;",
    234 : r"&#281;",
    235 : r"&euml;",
    236 : r"&#283;",
    237 : r"&iacute;",
    238 : r"&icirc;",
    239 : r"&#271;",
    240 : r"&#273;",
    241 : r"&#324;",
    242 : r"&#328;",
    243 : r"&oacute;",
    244 : r"&ocirc;",
    245 : r"&#337;",
    246 : r"&ouml;",
    247 : r"&divide;",
    248 : r"&#345;",
    249 : r"&#367;",
    250 : r"&uacute;",
    251 : r"&#369;",
    252 : r"&uuml;",
    253 : r"&yacute;",
    254 : r"&#355;",
    255 : r"&#729;"}
    #Encode.
    if action:
        for ch in s:
            try:
                my_ord = ord(ch)
                new += specialchars[my_ord]
            except KeyError:
                new += ch
    #Decode.
    else:   
        for my_ord in specialchars:
            pattern = specialchars[my_ord]
            replacement = chr(my_ord)
            s = re.sub(str(pattern), str(replacement), s)
        new = s
        
    return new

def test():    
    x = "I liek to !@$%^^&#~"
    print x
    x = url_escape(x, 1)
    print x
    x = url_escape(x, 0)
    print x
    x = "<b>fuck</b>"
    print x
    x = htmlspecialchars(x, 1)
    print x
    x = htmlspecialchars(x, 0)
    print x
