    ProxyRequests on
    <Proxy *>
            Order deny,allow
            Allow from all
    </Proxy>

    ProxyPreserveHost On

        ProxyPass /elasticsearch/ http://19.19.19.11:5601/elasticsearch/
            ProxyPassReverse /elasticsearch/ http://19.19.19.11:5601/elasticsearch/

            ProxyPass /app/kibana http://19.19.19.11:5601/app/kibana
            ProxyPassReverse /app/kibana http://19.19.19.11:5601/app/kibana

        ProxyPass /bundles/ http://19.19.19.11:5601/bundles/
            ProxyPassReverse /bundles/ http://19.19.19.11:5601/bundles/

        ProxyPass /built_assets/ http://19.19.19.11:5601/built_assets/
            ProxyPassReverse /built_assets/ http://19.19.19.11:5601/built_assets/


        ProxyPass /ui/ http://19.19.19.11:5601/ui/
            ProxyPassReverse /ui/ http://19.19.19.11:5601/ui/


        ProxyPass /node_modules/ http://19.19.19.11:5601/node_modules/
            ProxyPassReverse /node_modules/ http://19.19.19.11:5601/node_modules/

        ProxyPass /translations/ http://19.19.19.11:5601/translations/
            ProxyPassReverse /translations/ http://19.19.19.11:5601/translations/

         ProxyPass /api/ http://19.19.19.11:5601/api/
            ProxyPassReverse /api/ http://19.19.19.11:5601/api/

        ProxyPass /internal/ http://19.19.19.11:5601/internal/
            ProxyPassReverse /internal/ http://19.19.19.11:5601/internal/


        ProxyPass /goto/ http://19.19.19.11:5601/goto/
            ProxyPassReverse /goto/ http://19.19.19.11:5601/goto/

    #SSLProxyEngine on
    #SSLProxyVerify none
    #SSLProxyCheckPeerCN off
    #SSLProxyCheckPeerName off
    #SSLProxyCheckPeerExpire off