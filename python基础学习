# -*- coding: utf-8 -*-
# r''表示不转义
import math
from collections import Iterable
from functools import reduce
# python是一门动态语言
print(r'\\\t\\')
print('''aaa   
aaa   
aaa''')
print(len('aaa'))
r = (85-72)/72*100
print('成绩提升了%.2f%%' % r)
# creating a list
alist = ['b', 'a', 'd', 'c']
print(type(alist))
print(alist)
print(alist[0])
# 负索引返回列表末尾开始的元素
print(alist[-1])
# 切片 返回指定开始和结束索引的元素(右开区间)
# b = a[i:j] 表示复制a[i]到a[j-1]，以生成新的list对象

a = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
b = a[1:3]   # [1,2]

# 当i缺省时，默认为0，即 a[:3]相当于 a[0:3]
# 当j缺省时，默认为len(alist), 即a[1:]相当于a[1:10]
# 当i,j都缺省时，a[:]就相当于完整复制一份a

# b = a[i:j:s] 表示：i,j与上面的一样，但s表示步进，缺省为1.
# 所以a[i:j:1]相当于a[i:j]

# 当s<0时，i缺省时，默认为-1. j缺省时，默认为-len(a)-1
# 所以a[::-1]相当于 a[-1:-len(a)-1:-1]，也就是从最后一个元素到第一个元素复制一遍，即倒序。
print(a[0:2])
print(a[::-2])
# append()在列表末尾添加 一个 元素
alist.append('d')
print(alist)
# insert()指定位置添加一个元素
alist.insert(2, 'c')
print(alist)
# remove()删除列表中与给定值匹配的第一个匹配项
alist.remove('b')
print(alist)
# pop()删除指定索引的元素，若未指定索引，删除最后一个元素
alist.pop(1)
print(alist)
alist.pop()
print(alist)
# 排序 sort()
alist.sort()
print(alist)
# 合并列表
blist = ['e', 'f']
skills = alist + blist
print(skills)
# 列表解析
blist = ['e', 'f']
clist = [x*2 for x in blist]
print(clist)

# 元组 tuple 不可变的
my_list = (1,)
print(my_list)

# 字典 dict （map）key是不可变对象 数学意义上的无序和无重复元素的集合
my_map = {'bob': 75, 'tom': 66, 'lisa': 87}
print(my_map['bob'])
# 通过key放入
my_map['soal'] = 55
print(my_map)
# 查看key是否存在
print('bob' in my_map)
print(my_map.get('bo1b'))
my_map.pop('lisa')

# set 不可重复的key(重复自动过滤)
my_set = set([1, 2, 3, 1, 2, 3])
print(my_set)
# add添加
my_set.add(4)
print(my_set)
# remove删除
my_set.remove(4)
print(my_set)


# 定义一个函数
def my_test(x):
    if x > 50:
        return '当前数字大于五十'
    elif x < 50:
        return '当前数字小于五十'
    else:
        return '当前数字是五十'
print(my_test(55))

print(math.sqrt(2))


def power(x, n=2):
    num = 1
    while n > 0:
        n = n - 1
        num = x * num
    return num
print(power(5, 3))

# 默认参数必须指向不变对象！


def fact(n):
    if n == 1:
        return 1
    return n * fact(n - 1)

print(fact(10))


# def move(n, a, b, c):
#     if n == 1:
#         print(a, '-->', c)
#     else:
#         move(n-1, a, c, b)
#         move(1, a, b, c,)
#         move(n-1, b, a, c)
#
# move(5, 'A', 'B', 'C')

# 检查元素是否可以迭代
print(isinstance('132', Iterable))


def find_min_and_max(L):
    if len(L) == 0:
        return (None, None)
    max = L[0]
    min = L[0]
    for n in L:
        if n >= max:
            max = n
        if min >= n:
            min = n
    return min, max
# 测试
if find_min_and_max([]) != (None, None):
    print('测试失败!')
elif find_min_and_max([7]) != (7, 7):
    print('测试失败!')
elif find_min_and_max([7, 1]) != (1, 7):
    print('测试失败!')
elif find_min_and_max([7, 1, 3, 9, 5]) != (1, 9):
    print('测试失败!')
else:
    print('测试成功!')


L1 = ['Hello', 'World', 18, 'Apple', None]
L2 = [x.lower() for x in L1 if isinstance(x,str)]
# 测试:
print(L2)
if L2 == ['hello', 'world', 'apple']:
    print('测试通过!')
else:
    print('测试失败!')


def triangles():
    L = [1]
    while True:
        yield L
        L = [1] + [L[i] + L[i + 1] for i in range(len(L) - 1)] + [1]

# 期待输出:
# [1]
# [1, 1]
# [1, 2, 1]
# [1, 3, 3, 1]
# [1, 4, 6, 4, 1]
# [1, 5, 10, 10, 5, 1]
# [1, 6, 15, 20, 15, 6, 1]
# [1, 7, 21, 35, 35, 21, 7, 1]
# [1, 8, 28, 56, 70, 56, 28, 8, 1]
# [1, 9, 36, 84, 126, 126, 84, 36, 9, 1]
n = 0
results = []
for t in triangles():
    results.append(t)
    n = n + 1
    if n == 10:
        break

for t in results:
    print(t)

if results == [
    [1],
    [1, 1],
    [1, 2, 1],
    [1, 3, 3, 1],
    [1, 4, 6, 4, 1],
    [1, 5, 10, 10, 5, 1],
    [1, 6, 15, 20, 15, 6, 1],
    [1, 7, 21, 35, 35, 21, 7, 1],
    [1, 8, 28, 56, 70, 56, 28, 8, 1],
    [1, 9, 36, 84, 126, 126, 84, 36, 9, 1]
]:
    print('测试通过!')
else:
    print('测试失败!')


# 凡是可作用于for循环的对象都是Iterable类型；
#
# 凡是可作用于next()函数的对象都是Iterator类型，它们表示一个惰性计算的序列；
#
# 集合数据类型如list、dict、str等是Iterable但不是Iterator，不过可以通过iter()函数获得一个Iterator对象。

# map()函数
def normalize(name):
    return name[0].upper() + name[1:].lower()
# 测试:
L1 = ['adam', 'LISA', 'barT']
L2 = list(map(normalize, L1))
print(L2)

# reduce()函数


def prod(L):
    return reduce(pro, L)


def pro(x, y):
    return x * y

print('3 * 5 * 7 * 9 =', prod([3, 5, 7, 9]))
if prod([3, 5, 7, 9]) == 945:
    print('测试成功!')
else:
    print('测试失败!')


# map()函数 和 reduce()函数
def str2float(s):
    digits = {'0': 0, '1': 1, '2': 2, '3': 3, '4': 4, '5': 5, '6': 6, '7': 7, '8': 8, '9': 9}

    def char2num(c):
        return digits[c]

    def fn(x, y):
        return x * 10 + y

    pos = s.find('.')
    if pos >= 0:
        return (reduce(fn, map(char2num, s[:pos])) + reduce(fn, map(char2num, s[pos + 1:])) * (10 ** -len(s[pos + 1:])))
    return reduce(fn, map(char2num, s))

print('str2float(\'123.456\') =', str2float('123.456'))
if abs(str2float('123.456') - 123.456) < 0.00001:
    print('测试成功!')
else:
    print('测试失败!')


# filter()函数用于过滤序列
def is_palindrome(n):
    l1 = str(n)
    return l1 == l1[::-1]
# 测试:
output = filter(is_palindrome, range(1, 1000))
print('1~1000:', list(output))
if list(filter(is_palindrome, range(1, 200))) == [1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 22, 33, 44, 55, 66, 77, 88, 99, 101, 111, 121, 131, 141, 151, 161, 171, 181, 191]:
    print('测试成功!')
else:
    print('测试失败!')


# sorted()函数就可以对list进行排序
L = [('Bob', 75), ('Adam', 92), ('Bart', 66), ('Lisa', 88)]


# 按成绩高低排序
def by_score(t):
    return -t[1]
L2 = sorted(L, key=by_score)
print(L2)


# 闭包 返回函数不要引用任何循环变量，或者后续会发生变化的变量
# 利用闭包返回一个计数器函数，每次调用它返回递增整数：
def createcounter():
    cnt = [0]  # 将cnt设定为数组

    def counter():
        cnt[0] = cnt[0]+1  # 修改数组中的元素值
        return cnt[0]  # 返回修改的元素值
    return counter
# 测试:
counterA = createcounter()
print(counterA(), counterA(), counterA(), counterA(), counterA()) # 1 2 3 4 5
counterB = createcounter()
if [counterB(), counterB(), counterB(), counterB()] == [1, 2, 3, 4]:
    print('测试通过!')
else:
    print('测试失败!')

# 关键字lambda表示匿名函数，冒号前面的x表示函数参数
lambda x: x * x


def is_odd(n):
    return n % 2 == 1

L = list(filter(lambda n: n % 2 ==1, range(1, 20)))

# 在函数调用前后自动打印日志，但又不希望修改now()函数的定义，这种在代码运行期间动态增加功能的方式，称之为“装饰器”（Decorator）
import functools

def log(func):
    @functools.wraps(func)
    def wrapper(*args, **kw):
        print('call %s():' % func.__name__)
        return func(*args, **kw)
    return wrapper


def log(text):
    def decorator(func):
        @functools.wraps(func)
        def wrapper(*args, **kw):
            print('%s %s():' % (text, func.__name__))
            return func(*args, **kw)
        return wrapper
    return decorator


# 请把下面的Student对象的gender字段对外隐藏起来，用get_gender()和set_gender()代替，并检查参数有效性：
class Student(object):
    def __init__(self, name, gender):
        self.name = name
        self.__gender = gender

    def get_gender(self):
        return self.__gender

    def set_gender(self, name):
        self.__gender = name
# 测试:
bart = Student('Bart', 'male')
if bart.get_gender() != 'male':
    print('测试失败!')
else:
    bart.set_gender('female')
    if bart.get_gender() != 'female':
        print('测试失败!')
    else:
        print('测试成功!')


# 为了统计学生人数，可以给Student类增加一个类属性，每创建一个实例，该属性自动增加：
class Student(object):
    count = 0

    def __init__(self, name):
        self.name = name
        Student.count += 1
# 测试:
if Student.count != 0:
    print('测试失败!')
else:
    bart = Student('Bart')
    if Student.count != 1:
        print('测试失败!')
    else:
        lisa = Student('Bart')
        if Student.count != 2:
            print('测试失败!')
        else:
            print('Students:', Student.count)
            print('测试通过!')

# 使用__slots__来限制该class实例能添加的属性，仅对当前类实例起作用，对继承的子类是不起作用的


# 请利用@property给一个Screen对象加上width和height属性，以及一个只读属性resolution：
class Screen(object):
    @property
    def width(self):
        return self._width

    @width.setter
    def width(self, value):
        self._width = value

    @property
    def height(self):
        return self._height

    @width.setter
    def height(self, value):
        self._height = value

    @property
    def resolution(self):
        return self._width*self._height
# 测试:
s = Screen()
s.width = 1024
s.height = 768
print('resolution =', s.resolution)
if s.resolution == 786432:
    print('测试通过!')
else:
    print('测试失败!')

# 把Student的gender属性改造为枚举类型，可以避免使用字符串：
from enum import Enum, unique


class Gender(Enum):
    Male = 0
    Female = 1


class Student(object):
    def __init__(self, name, gender):
        self.name = name
        self.gender = gender
# 测试:
bart = Student('Bart', Gender.Male)
if bart.gender == Gender.Male:
    print('测试通过!')
else:
    print('测试失败!')

# try...except...finally...的错误处理机制
try:
    print('try...')
    r = 10 / 0
    print('result:', r)
except ZeroDivisionError as e:
    print('except:', e)
finally:
    print('finally...')
print('END')


# 断言（assert）
def foo(s):
    n = int(s)
    assert n != 0, 'n is zero!'
    return 10 / n


def main():
    foo('0')

# logging
# import logging
# logging.basicConfig(level=logging.INFO)
# logging的好处，它允许你指定记录信息的级别，有debug，info，warning，error等几个级别


# 启动Python的调试器pdb，程序以单步方式运行，可以随时查看运行状态

# 编写单元测试时，需要编写一个测试类，从unittest.TestCase继承
# 以test开头的方法就是测试方法，不以test开头的方法不被认为是测试方法，测试的时候不会被执行
# 对每一类测试都需要编写一个test_xxx()方法。由于unittest.TestCase提供了很多内置的条件判断，只需要调用这些方法就可以断言输出是否是我们所期望的。最常用的断言就是assertEqual()：
# self.assertEqual(abs(-1), 1)  # 断言函数返回的结果与1相等
# 另一种重要的断言就是期待抛出指定类型的Error，比如通过d['empty']访问不存在的key时，断言会抛出KeyError
# 通过d.empty访问不存在的key时，我们期待抛出AttributeError：

import unittest


class Student(object):
    def __init__(self, name, score):
        self.name = name
        self.score = score

    def get_grade(self):
        if self.score < 0 or self.score > 100 :
            raise ValueError
        if self.score >= 80:
            return 'A'
        if self.score >= 60:
            return 'B'
        return 'C'


class TestStudent(unittest.TestCase):

    def test_80_to_100(self):
        s1 = Student('Bart', 80)
        s2 = Student('Lisa', 100)
        self.assertEqual(s1.get_grade(), 'A')
        self.assertEqual(s2.get_grade(), 'A')

    def test_60_to_80(self):
        s1 = Student('Bart', 60)
        s2 = Student('Lisa', 79)
        self.assertEqual(s1.get_grade(), 'B')
        self.assertEqual(s2.get_grade(), 'B')

    def test_0_to_60(self):
        s1 = Student('Bart', 0)
        s2 = Student('Lisa', 59)
        self.assertEqual(s1.get_grade(), 'C')
        self.assertEqual(s2.get_grade(), 'C')

    def test_invalid(self):
        s1 = Student('Bart', -1)
        s2 = Student('Lisa', 101)
        with self.assertRaises(ValueError):
            s1.get_grade()
        with self.assertRaises(ValueError):
            s2.get_grade()

if __name__ == '__main__':
    unittest.main()


