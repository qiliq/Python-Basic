import socket

# 创建一个socket AF_INET指定使用IPv4协议 IPv6(AF_INET6) SOCK_STRESM指定使用面向流的TCP协议 SOCK_DGRAM(UDP协议)
s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
# 建立连接 参数是一个tuple，包含地址和端口号
s.connect(('127.0.0.1', 9999))
# 发送数据
print(s.recv(1024).decode('utf-8'))
for data in [b'Michael', b'Tracy', b'Sarah']:
    # 发送数据:
    s.send(data)
    print(s.recv(1024).decode('utf-8'))
s.send(b'exit')
s.close()
