import pytest
from selenium import webdriver
from datetime import datetime
import os

# Hook to capture screenshot on every test, pass or fail
@pytest.hookimpl(hookwrapper=True)
def pytest_runtest_makereport(item, call):
    pytest_html = item.config.pluginmanager.getplugin("html")
    outcome = yield
    report = outcome.get_result()
    extra = getattr(report, "extra", [])
    
    if report.when == "call":
        # Get driver from fixture logic if possible
        driver = item.funcargs.get('driver', None)
        if driver:
            # Create screenshots directory if it doesn't exist
            os.makedirs("screenshots", exist_ok=True)
            timestamp = datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
            file_name = f"screenshots/{item.name}_{timestamp}.png"
            driver.save_screenshot(file_name)
            
            # Attach screenshot to HTML report
            if pytest_html:
                # Add image HTML block directly to the extras report
                html_image = f'<img src="{file_name}" alt="screenshot" style="width: 400px; border: 1px solid #ccc; margin-top: 10px;" />'
                extra.append(pytest_html.extras.html(html_image))
        
        report.extra = extra

@pytest.fixture(scope="session")
def driver():
    """Initializes WebDriver for Chrome and returns the instance"""
    options = webdriver.ChromeOptions()

    # Hacer que la ventana se abra completa en pantalla (Maximizar) en lugar de una resolución estática
    options.add_argument('--start-maximized')
    
    driver = webdriver.Chrome(options=options)
    driver.maximize_window()
    driver.implicitly_wait(5)
    
    yield driver
    
    # Teardown: close WebDriver after all tests finish
    driver.quit()
