# Placeholder for future "Flask" app 
from flask import Flask

app = Flask(__name__)

@app.route('/')
def home():
    return "BudgetBuilder backend is under construction."

if __name__ == '__main__':
    app.run(debug=True)
