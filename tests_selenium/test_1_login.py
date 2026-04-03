import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import random
import string
import time

BASE_URL = "http://localhost/Task-Managerv2"
test_email = "admin@gmail.com"
test_password = "admin123"

class TestLoginFlow:
    """ US1: Inicio de Sesión """

    def test_01_login_flujo_negativo(self, driver):
        """[Prueba Negativa] Intentar iniciar sesión con credenciales inválidas"""
        driver.get(f"{BASE_URL}/auth/login.php")
        time.sleep(1) # Ralentizamos visualmente
        
        driver.find_element(By.NAME, "email").send_keys("noexisto@gmail.com")
        time.sleep(1)
        driver.find_element(By.NAME, "password").send_keys("1234")
        time.sleep(1)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        # Debe aparecer la alerta de Bootstrap de error
        error_msg = WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CSS_SELECTOR, ".alert.alert-danger"))
        )
        time.sleep(1)
        assert "Usuario no encontrado" in error_msg.text or "Clave incorrecta" in error_msg.text

    def test_02_login_flujo_positivo(self, driver):
        """[Happy Path / Prueba Positiva] Registrar e Iniciar Sesión con datos válidos"""
        driver.get(f"{BASE_URL}/auth/register.php")
        time.sleep(1)
        
        driver.find_element(By.NAME, "email").send_keys(test_email)
        driver.find_element(By.NAME, "password").send_keys(test_password)
        time.sleep(1)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(2) # Esperar a ver la alerta de registro exitoso
        
        driver.get(f"{BASE_URL}/auth/login.php")
        time.sleep(1)
        
        driver.find_element(By.NAME, "email").send_keys(test_email)
        driver.find_element(By.NAME, "password").send_keys(test_password)
        time.sleep(1)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        
        # Esperar hasta que cargue la tabla del dashboard en lugar del span para evitar errores de XPath
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.CLASS_NAME, "table"))
        )
        time.sleep(1)
        assert "dashboard" in driver.current_url
