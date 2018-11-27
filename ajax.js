/**
 * Created by isle on 2017/11/10.
 */

/**
 * @function 利用ajax动态交换数据
 * @param url   要提交请求的页面
 * @param jsonData  要提交的数据,利用Json传递
 * @param getMsg  这个函数可以获取到处理后的数据
 * @param type    接受的数据类型,text/xml/json
 * @param tagName type = xml 的时候这个参数设置为要获取的文本的标签名
 * @return 无
 */
function ajax(url,jsonData,getMsg,type,tagName)
{
    //创建Ajax对象,ActiveXObject兼容IE5,6
    var oAjax = window.XMLHttpRequest?new XMLHttpRequest():new ActiveXObject("Microsoft.XMLHTTP");
    //打开请求
    oAjax.open('POST',url,true);//方法,URL,异步传输
    //发送请求
    var data = '';
    for(var d in jsonData)   //拼装数据
        data += (d + '=' +jsonData[d]+'&');
    oAjax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    oAjax.send(data);
    //接收返回,当服务器有东西返回时触发
    oAjax.onreadystatechange = function ()
    {
        if(oAjax.readyState == 4 && oAjax.status == 200)
        {
            if(type == 'text')
            {
                if(getMsg) getMsg(oAjax.responseText);
            }
            else if(type == 'json')
            {
                var json = eval('('+oAjax.responseText+')');//把传回来的字符串解析成json对象
                //var json = JSON.parse(oAjax.responseText);//把传回来的字符串解析成json对象
                if(getMsg)getMsg(json);
            }
            else if(type == 'xml')
            {
                var oXml =  oAjax.responseXML; //返回的是一个XML DOM对象
                var oTag = oXml.getElementsByTagName(tagName);
                var tagValue = oTag[0].childNodes[0].nodeValue;
                if(getMsg)getMsg(tagValue);
            }

        }
    }
}
