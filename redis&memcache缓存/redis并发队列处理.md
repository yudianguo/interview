1、redis限流处理
>  计数器|令牌桶
2、并发处理方案
> 想用lpush压入队列，然后使用len判断长度，超出20失败。库存放入redis队列中消耗