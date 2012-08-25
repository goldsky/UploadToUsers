
    <tr[[+fd.class]]>
        <td style="width:16px;"><img src="[[+fd.image]]" alt="[[+fd.image]]" /></td>
        <td><a href="[[+fd.link]]"[[+fd.linkAttribute]]>
                [[!uploadtousers:default=`[[+fd.filename]]`? &path=`[[+fd.fullPath]]` &field=`title`]]
            </a>
            <span style="font-size:80%">([[+fd.count]] downloads)</span>
        </td>
        <td>[[+fd.sizeText]]</td>
        <td>[[+fd.date]]</td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3">[[!uploadtousers? &path=`[[+fd.fullPath]]` &field=`description`]]</td>
    </tr>
