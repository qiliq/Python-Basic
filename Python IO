# 读文件

# Config.json
#
# {
#     "Server": [
#         {
#             "name": "Redis-localhost",
#             "ip": "10.167.1.1",
#             "port": 6379,
#             "auth": ""
#         }
#     ]
# }
#
# 标示符'r'表示读
# 调用read()方法可以一次读取文件的全部内容，Python把内容读到内存，用一个str对象表示

# try:
#     f = open('/path/to/file', 'r')
#     print(f.read())
# finally:
#     if f:
#         f.close()


with open('C:/Users/Desktop/Config.json', 'r') as config:
    print(config.read())

# 调用read()会一次性读取文件的全部内容（可能内存溢出）保险起见，可以反复调用read(size)方法，每次最多读取size个字节的内容
# 调用readline()可以每次读取一行内容，调用readlines()一次读取所有内容并按行返回list
# 文件很小，read()一次性读取最方便；如果不能确定文件大小，反复调用read(size)比较保险；如果是配置文件，调用readlines()最方便

#     for line in f.readlines():
#         print(line.strip())   把末尾的'\n'删掉

# 要读取二进制文件，比如图片、视频等等，用'rb'模式打开文件即可
# f = open('/Users/michael/test.jpg', 'rb')

# 要读取非UTF-8编码的文本文件，需要给open()函数传入encoding参数，例如，读取GBK编码的文件
# f = open('/Users/michael/gbk.txt', 'r', encoding='gbk')

# 遇到有些编码不规范的文件，可能会遇到UnicodeDecodeError，遇到这种情况，open()函数还接收一个errors参数，处理最简单的方式是直接忽略
# f = open('/Users/michael/gbk.txt', 'r', encoding='gbk', errors='ignore')


# 写文件

# 以'w'模式写入文件时，如果文件已存在，会直接覆盖（相当于删掉后新写入一个文件）。
# 如果希望追加到文件末尾可以传入'a'以追加（append）模式写入。
# with open('/Users/michael/test.txt', 'w') as f:
#     f.write('Hello, world!')


# 内存中的读写

# StringIO：在内存中读写str，write()方法用于写入str,getvalue()方法用于获得写入后的str
from io import StringIO
f = StringIO()
f.write('hello')
f.write(' ')
f.write('world!')
print(f.getvalue())

# StringIO操作的只能是str，如果要操作二进制数据，就需要使用BytesIO (经过UTF-8编码的bytes)
from io import BytesIO
f = BytesIO()
f.write('中文'.encode('utf-8'))
print(f.getvalue())


# 操作文件和目录

import os
print(os.name)
# posix，是Linux、Unix或Mac OS X，
# nt，是Windows系统

# 获取详细的系统信息，可以调用uname()函数,uname()函数在Windows上不提供

# os.environ获取系统的环境变量
print(os.environ.get('PATH'))
# 查看当前目录的绝对路径:
print(os.path.abspath('.'))

# 在某个目录下创建一个新目录，首先把新目录的完整路径表示出来:
# os.path.join('/Users/michael', 'testdir')

# 然后创建一个目录:
# os.mkdir('/Users/michael/testdir')

# 删掉一个目录:
# os.rmdir('/Users/michael/testdir')

# 两个路径合成一个时，不要直接拼字符串，而要通过os.path.join()函数
# 要拆分路径时，要通过os.path.split()函数,后一部分总是最后级别的目录或文件名
# os.path.splitext()可以直接让你得到文件扩展名
# 不要求目录和文件要真实存在
print(os.path.splitext('/path/to/file.txt'))

# 对文件重命名:
# os.rename('test.txt', 'test.py')
# 删掉文件:
# os.remove('test.py')
# 利用Python的特性来过滤文件。比如我们要列出当前目录下的所有目录
print([x for x in os.listdir('.') if os.path.isfile(x) and os.path.splitext(x)[1] == '.py'])


# 把变量从内存中变成可存储或传输的过程称之为序列化,在Python中叫pickling
# 把变量内容从序列化的对象重新读到内存里称之为反序列化，即unpickling
# Python提供了pickle模块来实现序列化
import pickle
d = dict(name='Bob', age=20, score=88)
print(pickle.dumps(d))
# pickle.dumps()方法把任意对象序列化成一个bytes,就可以把这个bytes写入文件
# 或者用另一个方法pickle.dump()直接把对象序列化后写入一个file-like Object

# 把对象从磁盘读到内存时，可以先把内容读到一个bytes，然后用pickle.loads()方法反序列化出对象
# 也可以直接用pickle.load()方法从一个file-like Object中直接反序列化出对象

# 要在不同的编程语言之间传递对象，就必须把对象序列化为标准格式，如XML，但更好的方法是序列化为JSON
# JSON表示出来就是一个字符串，可以被所有语言读取，也可以方便地存储到磁盘或者通过网络传输
# JSON不仅是标准格式，并且比XML更快，而且可以直接在Web页面中读取，非常方便
# JSON类型	   Python类型
#   {}	         dict
#   []	         list
# "string"	     str
# 1234.56	  int或float
# true/false  True/False
#  null	         None
import json
d = dict(name='Bob', age='20', score='88')
print(json.dumps(d))


with open('C:/Users/Desktop/Config.json', 'r') as config:
   print(json.loads(config.read()))


# 将对象序列化为JOSN
# 可选参数default就是把任意一个对象变成一个可序列为JSON的对象，只需要为Student专门写一个转换函数，再把函数传进去
class Student(object):
    def __init__(self, name, age, score):
        self.name = name
        self.age = age
        self.score = score

s = Student('Bob', 20, 88)


def student2dict(std):
    return {
        'name': std.name,
        'age': std.age,
        'score': std.score
    }
# print(json.dumps(s, default=student2dict))
# 把任意class的实例变为dict
print(json.dumps(s, default=lambda obj: obj.__dict__))


# 将序列化对象转换为对象
def dict2student(d):
    return Student(d['name'], d['age'], d['score'])
json_str = '{"age": 20, "score": 88, "name": "Bob"}'
print(json.loads(json_str, object_hook=dict2student))

