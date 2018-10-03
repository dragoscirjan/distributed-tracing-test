import cherrypy

from tracer.controller.controller2php import Controller2Php

cherrypy.config.update({'server.socket_host': '0.0.0.0', 'server.socket_port': 8099})

if __name__ == '__main__':
    cherrypy.quickstart(Controller2Php())


# https://www.rapidtables.com/convert/number/hex-to-decimal.html

# # Hello World program in Python
    
# def _convert_hex_str_to_int(val):
#     """Convert hexadecimal formatted ids to signed int64"""
#     if val is None:
#         return None

#     hex_num = int(val, 16)
#     #  ensure it fits into 64-bit
#     if hex_num > 0x7FFFFFFFFFFFFFFF:
#         hex_num -= 0x10000000000000000

#     assert -9223372036854775808 <= hex_num <= 9223372036854775807
#     return hex_num
    
# trace_id = '7bba131ec63747139faca910a8300e43'

# print([trace_id[0:16], trace_id[16:32]])
# print([_convert_hex_str_to_int(trace_id[0:16]), _convert_hex_str_to_int(trace_id[16:32])])