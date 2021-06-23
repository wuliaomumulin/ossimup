
```
# 这里查看目前管道
GET _ingest/pipeline

GET /zn-alerts-2020.11.02/_doc/1
DELETE /zn-alerts-2020.11.02/_doc/1

# 这里是新建管道
PUT _ingest/pipeline/firstpipeline
{
  "description": "第一个",
  
  "processors": [
    {
      "uppercase": {
        "field": "message"
      }
    },
    {
      "rename" :{
        "field": "name",
        "target_field": "名称"
      }
    }
  ]
  
}

# 这里是做管道测试
POST _ingest/pipeline/firstpipeline/_simulate?verbose
{
  "docs": [
      {
        "_source":{
          "name":"测试1",
          "message":"天气很冷"
        }
      }
    ]
}

# 这里是添加数据
PUT zn-alerts-2020.11.02/_doc/1?pipeline=firstpipeline
{
  "name":"测试2",
  "message":"this is cool"
}
```