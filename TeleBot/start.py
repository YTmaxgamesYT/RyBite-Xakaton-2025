from aiogram import Bot, Dispatcher, types
from aiogram.filters import Command
from aiogram.types import ReplyKeyboardMarkup, KeyboardButton, WebAppInfo
from aiogram.utils.keyboard import InlineKeyboardBuilder
from aiogram.enums import ParseMode, ContentType
import asyncio

# Замените на ваш токен
API_TOKEN = '8045100599:AAF3x99WK5s32su-Mk4yVAazKPHY7sFQQS8'

# Инициализация бота и диспетчера
bot = Bot(token=API_TOKEN)
dp = Dispatcher()


# Команда /start
@dp.message(Command("start"))
async def cmd_start(message: types.Message):
    # Создаем кнопку с WebApp
    builder = InlineKeyboardBuilder()
    builder.row(types.InlineKeyboardButton(
        text="Открыть EmployMoni",
        web_app=WebAppInfo(url=f"https://dobrovnytru.ru/mobile_checkout.php?tg_id={message.from_user.id}")
        # Замените на ваш URL
    ))

    # Отправляем сообщение с кнопкой
    await message.answer(
        f"""
        Добро пожаловать в EmployMoni!

Ваш ID: {message.from_user.id}

Нажмите на кнопку, чтобы открыть сервис
        """,
        reply_markup=builder.as_markup()
    )



# Запуск бота
async def main():
    await dp.start_polling(bot)


if __name__ == '__main__':
    asyncio.run(main())