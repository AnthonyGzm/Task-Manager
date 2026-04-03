import pytest
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
import time

BASE_URL = "http://localhost/Task-Managerv2"

class TestLogoutFlow:
    """ US6: Cierre de Sesión """

    def test_09_logout_flujo_positivo(self, driver):
        """[Happy Path / Prueba Positiva] Hacer clic en Cerrar Sesión y verificar redirección a Login"""
        driver.get(f"{BASE_URL}/dashboard.php")
        time.sleep(1)
        
        # Buscar el botón rojo de Logout que abre el modal
        logout_trigger = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.CSS_SELECTOR, "button[data-bs-target='#logoutModal']"))
        )
        time.sleep(1)
        logout_trigger.click()
        
        # Esperar a que el modal emerja y confirmar la salida
        confirm_logout_btn = WebDriverWait(driver, 5).until(
            EC.element_to_be_clickable((By.ID, "confirmLogoutBtn"))
        )
        time.sleep(1)
        confirm_logout_btn.click()
        
        # Verificar que el sistema destruyó la sesión y nos devolvió a la página de inicio o login
        WebDriverWait(driver, 5).until(
            EC.presence_of_element_located((By.NAME, "email"))
        )
        time.sleep(1)
        assert "login" in driver.current_url

    def test_10_proteccion_rutas_flujo_negativo(self, driver):
        """[Prueba Negativa] Intentar entrar al dashboard mediante URL después de haber cerrado sesión"""
        # Intentamos forzar la entrada al panel administrativo por enlace directo
        driver.get(f"{BASE_URL}/dashboard.php")
        time.sleep(1)
        
        # El backend nos debe rebotar velozmente al login si la protección ($_SESSION) está funcionando bien
        assert "login" in driver.current_url
