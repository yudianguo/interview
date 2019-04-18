

### elk 应用场景  

1、监控系统日志、业务日志信息。

2、可视化日志内容。查看访问量，下单量信息。

### 概述

ELK是一套解决方案而不是一款软件， 三个字母分别是三个软件产品的缩写。

E代表Elasticsearch,负责日志的存储和检索； 

L代表Logstash, 负责日志的收集，过滤和格式化；

K代表Kibana，负责日志的展示统计和数据可视化。

Kafka 是一种分布式的，基于发布 / 订阅的消息系统。作用**类似于缓存**，即活跃的数据和离线

filebeats 轻量级的日志传输工具

### 架构

![WX20181229-141512](/Users/yudianguo/srv/rencai/interview/image/WX20181229-141512.png)


### 环境配置

---

### filebeat

配置

```vb
- module: nginx
  # Access logs
  access:
    enabled: true
    var.paths: ["/usr/share/filebeat/data/nginx/access.log*"]
    # Set custom paths for the log files. If left empty,
    # Filebeat will choose the paths depending on your OS.
    #var.paths:

  # Error logs
  error:
    enabled: true
    var.paths: ["/usr/share/filebeat/data/nginx/error.log*"]
```

工作原理

启动Filebeat时，它会启动一个或多个查找器，查看您为日志文件指定的本地路径。 对于prospector 所在的每个日志文件，prospector 启动harvester。 每个harvester都会为新内容读取单个日志文件，并将新日志数据发送到libbeat，后者将聚合事件并将聚合数据发送到您为Filebeat配置的输出。

![WX20181229-143321](/Users/yudianguo/srv/rencai/interview/image/WX20181229-143321.png)

### harvester

harvester :负责读取单个文件的内容。读取每个文件，并将内容发送到 the output
 每个文件启动一个harvester, harvester 负责打开和关闭文件，这意味着在运行时文件描述符保持打开状态
 如果文件在读取时被删除或重命名，Filebeat将继续读取文件。
 这有副作用，即在harvester关闭之前，磁盘上的空间被保留。默认情况下，Filebeat将文件保持打开状态，直到达到close_inactive状态

关闭harvester会产生以下结果：
 1）如果在harvester仍在读取文件时文件被删除，则关闭文件句柄，释放底层资源。
 2）文件的采集只会在scan_frequency过后重新开始。
 3）如果在harvester关闭的情况下移动或移除文件，则不会继续处理文件。

要控制收割机何时关闭，请使用close_ *配置选项

## prospector

prospector 负责管理harvester并找到所有要读取的文件来源。
 如果输入类型为日志，则查找器将查找路径匹配的所有文件，并为每个文件启动一个harvester。
 每个prospector都在自己的Go协程中运行。

Filebeat目前支持两种prospector类型：log和stdin。
 每个prospector类型可以定义多次。
 日志prospector检查每个文件以查看harvester是否需要启动，是否已经运行，
 或者该文件是否可以被忽略（请参阅ignore_older）。
 只有在harvester关闭后文件的大小发生了变化，才会读取到新行。

## Filebeat如何保持文件的状态

Filebeat  保存每个文件的状态并经常将状态刷新到磁盘上的注册文件中。
 该状态用于记住harvester正在读取的最后偏移量，并确保发送所有日志行。
 如果输出（例如Elasticsearch或Logstash）无法访问，Filebeat会跟踪最后发送的行，并在输出再次可用时继续读取文件。
 在Filebeat运行时，每个prospector内存中也会保存的文件状态信息，
 当重新启动Filebeat时，将使用注册文件的数据来重建文件状态，Filebeat将每个harvester在从保存的最后偏移量继续读取。

每个prospector为它找到的每个文件保留一个状态。
 由于文件可以被重命名或移动，因此文件名和路径不足以识别文件。
 对于每个文件，Filebeat存储唯一标识符以检测文件是否先前已采集过。

如果您的使用案例涉及每天创建大量新文件，您可能会发现注册文件增长过大。请参阅注册表文件太大？编辑有关您可以设置以解决此问题的配置选项的详细信息。

## Filebeat如何确保至少一次交付

Filebeat保证事件至少会被传送到配置的输出一次，并且不会丢失数据。 Filebeat能够实现此行为，因为它将每个事件的传递状态存储在注册文件中。

在输出阻塞或未确认所有事件的情况下，Filebeat将继续尝试发送事件，直到接收端确认已收到。

如果Filebeat在发送事件的过程中关闭，它不会等待输出确认所有收到事件。
 发送到输出但在Filebeat关闭前未确认的任何事件在重新启动Filebeat时会再次发送。
 这可以确保每个事件至少发送一次，但最终会将重复事件发送到输出。
 也可以通过设置shutdown_timeout选项来配置Filebeat以在关闭之前等待特定时间。

注意：
 Filebeat的至少一次交付保证包括日志轮换和删除旧文件的限制。如果将日志文件写入磁盘并且写入速度超过Filebeat可以处理的速度，或者在输出不可用时删除了文件，则可能会丢失数据。
 在Linux上，Filebeat也可能因inode重用而跳过行。有关inode重用问题的更多详细信息，请参阅filebeat常见问题解答。

---

### kafka介绍

### kafka 应用场景
* 日志收集：一个公司可以用Kafka可以收集各种服务的log，通过kafka以统一接口服务的方式开放给各种consumer，例如hadoop、Hbase、Solr等。
* 消息系统：解耦和生产者和消费者、缓存消息等。
* 用户活动跟踪：Kafka经常被用来记录web用户或者app用户的各种活动，如浏览网页、搜索、点击等活动，这些活动信息被各个服务器发布到kafka的topic中，然后订阅者通过订阅这些topic来做* * 实时的监控分析，或者装载到hadoop、数据仓库中做离线分析和挖掘。
* 运营指标：Kafka也经常用来记录运营监控数据。包括收集各种分布式应用的数据，生产各种操作的集中反馈，比如报警和报告。
* 流式处理：比如spark streaming和storm

处理系统之间的缓存设计目标：

- 以时间复杂度为 O(1) 的方式提供消息持久化能力，即使对 TB 级以上数据也能保证常数时间复杂度的访问性能。

- 高吞吐率。即使在非常廉价的商用机器上也能做到单机支持每秒 100K 条以上消息的传输。

- 支持 Kafka Server 间的消息分区，及分布式消费，同时保证每个 Partition 内的消息顺序传输。

- 同时支持离线数据处理和实时数据处理。

- Scale out：支持在线水平扩展。
  角色描述：

- Topic:指kafka处理消息源的不同分类，或称为一个主题，可以理解为MQ(消息队列)的一个名字        

- Broker:缓存代理，kafka集群中的一台或多台服务器统称为broker,一个broker可以容纳多个topic        

- Producer:消息的生产者,就是向kafka broker发消息的客户端(push)      

- Consumer:消息消费者，就是向kafak broker取消息的客户端(通过pull进行取)   

-     Message:消息，是通信的基本单位，每个producer可以向topic(主题)发布一些消息    

-    Offset(起始偏移量):kafka的存储文件都是按照offset.kafka来命名,用offset做名字的好处是方便查找。例如你想找位于2049的位置，只要找到2048.kafka的文件即可。当然the first offset就是00000000000.kafka      

- Partition:Topic物理上的分组，为了实现扩展性，一个非常大的topic可以分布到多个broker(服务器)上，一个topic可以分为多个partition,每个partition是一个有序的队列。partition中的每条消息都会被分配一个有序的Id(offset).kafka只保证一个partition中的顺序将消息发送给consumer，不保证一个topic的整体(多个partition间)的顺序，也就是说，一个topic在集群中可以有多个partition,那么分区的策略是什么？(消息发送到哪个分区上，有两种策略，一是采用Key Hash算法，二是采用Round Robin算法)(两种算法请自行Google)

  ![WX20181229-115713](/Users/yudianguo/srv/rencai/interview/image/WX20181229-115713.png)

可替代产品：rabbitMq、Redis

---
### logstash
介绍

jruby语言编写的运行在java虚拟机上的具有收集
分析转发数据流功能的工具。Logstash 的作用就是一个数据收集器，将各种格式各种渠道的数据通过它收集解析之后格式化输出到 Elasticsearch ，最后再由
Kibana 提供的比较友好的 Web 界面进行汇总、分析、搜索。

* 能集中处理各种类型的数据

* 能标准化不通模式和格式的数据

* 能快速的扩展自定义日志的格式

* 能非常方便的添加插件来自定义数据

  ![WX20181229-145408](/Users/yudianguo/srv/rencai/interview/image/WX20181229-145408.png)



# Elasticsearch介绍

简介
Elasticsearch是一个高可扩展的开源全文搜索和分析引擎，它允许存储、搜索和分析大量的数据，并且这个过程是近实时的。它通常被用作底层引擎和技术，为复杂的搜索功能和要求提供动力。

- ElasticSearch的一些概念:

1. 集群 (cluster)

   在一个分布式系统里面,可以通过多个elasticsearch运行实例组成一个集群,这个集群里面有一个节点叫做主节点(master),elasticsearch是去中心化的,所以这里的主节点是动态选举出来的,不存在单点故障。

   在同一个子网内，只需要在每个节点上设置相同的集群名,elasticsearch就会自动的把这些集群名相同的节点组成一个集群。节点和节点之间通讯以及节点之间的数据分配和平衡全部由elasticsearch自动管理。

   在外部看来elasticsearch就是一个整体。

2. 节点(node)

   每一个运行实例称为一个节点,每一个运行实例既可以在同一机器上,也可以在不同的机器上.所谓运行实例,就是一个服务器进程.在测试环境内,可以在一台服务器上运行多个服务器进程,在生产环境建议每台服务器运行一个服务器进程

3. 索引(index)

   这里的索引是名词不是动词,在elasticsearch里面支持多个索引。类似于关系数据库里面每一个服务器可以支持多个数据库一样。在每一索引下面又支持多种类型，类似于关系数据库里面的一个数据库可以有多张表。但是本质上和关系数据库有很大的区别。这里暂时可以这么理解

4. 分片(shards)

   把一个索引分解为多个小的索引，每一个小的索引叫做分片。分片后就可以把各个分片分配到不同的节点中

5. 副本(replicas)

   每一个分片可以有0到多个副本，每个副本都是分片的完整拷贝，可以用来增加速度，同时也可以提高系统的容错性，一旦某个节点数据损坏，其他节点可以代替他.



## Kibana简介

Kibana是一个为Elasticsearch平台分析和可视化的开源平台，使用Kibana能够搜索、展示存储在Elasticsearch中的索引数据。使用它可以很方便用图表、表格、地图展示和分析数据。
 Kibana能够轻松处理大量数据，通过浏览器接口能够轻松的创建和分享仪表盘，通过改变Elasticsearch查询时间，可以完成动态仪表盘。

# 

### 总结

基于 ELK stack 的日志解决方案的优势主要体现于：

* 可扩展性：采用高可扩展性的分布式系统架构设计，可以支持每日 TB 级别的新增数据。

* 使用简单：通过用户图形界面实现各种统计分析功能，简单易用，上手快

* 快速响应：从日志产生到查询可见，能达到秒级完成数据的采集、处理和搜索统计。

* 界面炫丽：Kibana 界面上，只需要点击鼠标，就可以完成搜索、聚合功能，生成炫丽的仪表板。

  ![Kibana5](/Users/yudianguo/srv/rencai/interview/image/Kibana5.png)


> [ELK日志分析系统简介](https://www.jianshu.com/p/09beacb7dbf6)
>
> [filebeat工作原理](https://www.jianshu.com/p/6282b04fe06a)