FROM sebp/elk

ENV TZ=Europe/Moscow

COPY ./elk-post-hooks.sh /usr/local/bin/elk-post-hooks.sh
COPY ./logstash/context/project_policy_template.json /tmp/logstash/project_policy_template.json
COPY ./logstash/pipeline /etc/logstash/conf.d
