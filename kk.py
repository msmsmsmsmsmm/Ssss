from sin import SinAPI
api = SinAPI()
res = api.get_name("7830922155", "964")
if res:
    print(f"{res}")
else:
    print("not")
