<?php

use Illuminate\Http\UploadedFile;
use Carbon\Carbon;

class TranslationTest extends TestCase
{

    public function testAttributesPlaceholder()
    {
        $this->assertEquals('Hello, Jane!', trans('test::test.hello', ['name' => 'Jane']));
        $this->assertEquals('Hello, Jon!', trans('test::test.hello', ['name' => 'Jon']));
    }

    public function testPlurals()
    {
        $this->assertEquals('no apples', trans_choice('test::test.apples', 0));
        $this->assertEquals('1 apple', trans_choice('test::test.apples', 1));
        $this->assertEquals('2 apples', trans_choice('test::test.apples', 2));
    }

    public function testPluralsOffset()
    {
        $this->assertEquals('You do not like this yet', trans_choice('test::test.offset', 0));
        $this->assertEquals('You liked this', trans_choice('test::test.offset', 1));
        $this->assertEquals('You and one other person liked this', trans_choice('test::test.offset', 2));
        $this->assertEquals('You and 2 others liked this', trans_choice('test::test.offset', 3));
    }

    public function testSelect()
    {
        $this->assertEquals('He has two legs and is male!', trans('test::test.select', ['gender' => 'male']));
        $this->assertEquals('She has two legs and is female!', trans('test::test.select', ['gender' => 'female']));
        $this->assertEquals('It has two legs and is penguin!', trans('test::test.select', ['gender' => 'penguin']));
    }

    public function testValidatorMessages()
    {
        $imgDir = $this->fixturesPath . '/images';

        // Create
        if (File::exists($imgDir)) {
            File::deleteDirectory($imgDir);
        }

        File::makeDirectory($imgDir);

        $faker = Faker\Factory::create($this->app['config']['locale']);
        $fixturesPath = $this->fixturesPath;

        $data = [
            'accepted' => false,
            'required_if_cond' => true,
            'active_url' => 'thispage',
            'after_date' => Carbon::now()->toDateTimeString(),
            'alpha' => 123,
            'alpha_dash' => '&*',
            'alpha_num' => '_-',
            'array' => '123',
            'before_date' => Carbon::now()->toDateString(),
            'between_numeric' => 10,
            'between_file' => new UploadedFile($fixturesPath . '/texts/test_between.txt', str_random(), null, null, null, true),
            'between_string' => str_random(10),
            'between_array' => range(1, 10),
            'boolean' => 'string',
            'confirmed_confirmation' => 'b',
            'confirmed' => 'a',
            'date' => 'somestring',
            'date_format' => 'somestring',
            'different' => 'somestring',
            'digits' => 123456,
            'digits_between' => 124567,
            'distinct' => [
                1, 1,
                2, 2,
                3, 3,
            ],
            'email' => 'notanemail',
            'filled' => '',
            'image' => new UploadedFile($fixturesPath . '/texts/test_between.txt', str_random(), null, null, null, true),
            'in' => 'c',
            'in_array' => 4,
            'integer' => 4.20,
            'ip' => 'a.a.a.a',
            'json' => '{a:c}',
            'max_numeric' => 1234567,
            'max_file_plural' => new UploadedFile($fixturesPath . '/texts/test_between.txt', str_random(), null, 0, null, true),
            'max_string_plural' => 'abcdefghijklmn',
            'max_array_plural' => [1, 2, 3, 4, 5, 6],
            'max_file_singular' => new UploadedFile($fixturesPath . '/texts/test_between.txt', str_random(), null, 5, null, true),
            'max_string_singular' => 'string|max:1',
            'max_array_singular' => [1, 2],
            'mimes' => new UploadedFile($faker->image($this->fixturesPath . '/images'), str_random(), null, null, null, true),
            'min_numeric' => 1,
            'min_file' => new UploadedFile($fixturesPath . '/texts/test_empty.txt', str_random(), null, null, null, true),
            'min_string' => 'a',
            'min_array' => [1],
            'numeric' => 'qwe',
            'regex' => '-_!@#$%^&*()-=',
            'required' => [],
            'required_if' => null,
            'required_with' => '',
            'required_with_all' => '',
            'required_without' => '',
            'required_without_all' => '',
            'same' => 'notthesame',
            'size_numeric' => 12,
            'size_file_plural' => new UploadedFile($fixturesPath . '/texts/test_empty.txt', str_random(), null, null, null, true),
            'size_string_plural' => 'ab',
            'size_array_plural' => ['a', 'b'],
            'size_file_singular' => new UploadedFile($fixturesPath . '/texts/test_empty.txt', str_random(), null, null, null, true),
            'size_string_singular' => 'ab',
            'size_array_singular' => ['a', 'b'],
            'string' => 1234,
            'timezone' => 'NotATimezone',
        ];

        $rules = [
            'accepted' => 'accepted',
            'active_url' => 'active_url',
            'after_date' => 'after:tomorrow',
            'alpha' => 'alpha',
            'alpha_dash' => 'alpha_dash',
            'alpha_num' => 'alpha_num',
            'array' => 'array',
            'before_date' => 'before:yesterday',
            'between_numeric' => 'numeric|between:1,5',
            'between_file' => 'file|between:1,5',
            'between_string' => 'between:1,5',
            'between_array' => 'array|between:1,5',
            'boolean' => 'boolean',
            'confirmed' => 'confirmed',
            'required' => 'required',
            'date' => 'date',
            'date_format' => 'date_format:d-m-Y H:i:s',
            'different' => 'different:date',
            'digits' => 'digits:5',
            'digits_between' => 'digits_between:1,3',
            'distinct.*' => 'distinct',
            'email' => 'email',
            'filled' => 'filled',
            'image' => 'image',
            'in' => 'in:a,b',
            'in_array' => 'in_array:distinct',
            'integer' => 'integer',
            'ip' => 'ip',
            'json' => 'json',
            'max_numeric' => 'numeric|max:5',
            'max_file_plural' => 'file|max:5',
            'max_string_plural' => 'string|max:5',
            'max_array_plural' => 'array|max:5',
            'max_file_singular' => 'file|max:1',
            'max_string_singular' => 'string|max:1',
            'max_array_singular' => 'array|max:1',
            'mimes' => 'mimes:txt,avi,html',
            'min_numeric' => 'numeric|min:5',
            'min_file' => 'file|min:5',
            'min_string' => 'string|min:5',
            'min_array' => 'array|min:5',
            'numeric' => 'numeric',
            'present' => 'present',
            'regex' => 'regex:/^([\w\d]+?)$/',
            'required' => 'required',
            'required_if' => 'required_if:in,c',
            'required_with' => 'required_with:email,ip',
            'required_with_all' => 'required_with_all:email,ip',
            'required_without' => 'required_without:filled,required',
            'required_without_all' => 'required_without_all:filled,required',
            'same' => 'same:date',
            'size_numeric' => 'numeric|size:5',
            'size_string_plural' => 'string|size:5',
            'size_file_plural' => 'file|size:5',
            'size_array_plural' => 'array|size:5',
            'size_string_singular' => 'string|size:1',
            'size_file_singular' => 'file|size:1',
            'size_array_singular' => 'array|size:1',
            'string' => 'string',
            'timezone' => 'timezone'
        ];

        $validator = Validator::make($data, $rules);
        $errors = $validator->getMessageBag();

        $this->assertFalse($validator->passes());
        $this->assertNotEmpty($errors);
        $this->assertEquals('The accepted must be accepted.', $errors->first('accepted'));
        $this->assertEquals('The active url is not a valid URL.', $errors->first('active_url'));
        $this->assertEquals('The after date must be a date after tomorrow.', $errors->first('after_date'));
        $this->assertEquals('The alpha may only contain letters.', $errors->first('alpha'));
        $this->assertEquals('The alpha dash may only contain letters, numbers, and dashes.', $errors->first('alpha_dash'));
        $this->assertEquals('The alpha num may only contain letters and numbers.', $errors->first('alpha_num'));
        $this->assertEquals('The array must be an array.', $errors->first('array'));
        $this->assertEquals('The before date must be a date before yesterday.', $errors->first('before_date'));
        $this->assertEquals('The between numeric must be between 1 and 5.', $errors->first('between_numeric'));
        $this->assertEquals('The between file must be between 1 and 5 kilobytes.', $errors->first('between_file'));
        $this->assertEquals('The between string must be between 1 and 5 characters.', $errors->first('between_string'));
        $this->assertEquals('The between array must have between 1 and 5 items.', $errors->first('between_array'));
        $this->assertEquals('The boolean field must be true or false.', $errors->first('boolean'));
        $this->assertEquals('The confirmed confirmation does not match.', $errors->first('confirmed'));
        $this->assertEquals('The date is not a valid date.', $errors->first('date'));
        $this->assertEquals('The date format does not match the format d-m-Y H:i:s.', $errors->first('date_format'));
        $this->assertEquals('The different and date must be different.', $errors->first('different'));
        $this->assertEquals('The digits must be 5 digits.', $errors->first('digits'));
        $this->assertEquals('The digits between must be between 1 and 3 digits.', $errors->first('digits_between'));
        $this->assertEquals('The distinct.0 field has a duplicate value.', $errors->first('distinct.0'));
        $this->assertEquals('The email must be a valid email address.', $errors->first('email'));
        $this->assertEquals('The filled field is required.', $errors->first('filled'));
        $this->assertEquals('The image must be an image.', $errors->first('image'));
        $this->assertEquals('The selected in is invalid.', $errors->first('in'));
        $this->assertEquals('The in array field does not exist in distinct.', $errors->first('in_array'));
        $this->assertEquals('The integer must be an integer.', $errors->first('integer'));
        $this->assertEquals('The ip must be a valid IP address.', $errors->first('ip'));
        $this->assertEquals('The json must be a valid JSON string.', $errors->first('json'));
        $this->assertEquals('The max numeric may not be greater than 5.', $errors->first('max_numeric'));
        $this->assertEquals('The max file singular may not be greater than 1 kilobyte.', $errors->first('max_file_singular'));
        $this->assertEquals('The max string singular may not be greater than 1 character.', $errors->first('max_string_singular'));
        $this->assertEquals('The max array singular may not have more than 1 item.', $errors->first('max_array_singular'));
        $this->assertEquals('The max file plural may not be greater than 5 kilobytes.', $errors->first('max_file_plural'));
        $this->assertEquals('The max string plural may not be greater than 5 characters.', $errors->first('max_string_plural'));
        $this->assertEquals('The max array plural may not have more than 5 items.', $errors->first('max_array_plural'));
        $this->assertEquals('The mimes must be a file of type: txt,avi,html.', $errors->first('mimes'));
        $this->assertEquals('The min numeric must be at least 5.', $errors->first('min_numeric'));
        $this->assertEquals('The min file must be at least 5 kilobytes.', $errors->first('min_file'));
        $this->assertEquals('The min string must be at least 5 characters.', $errors->first('min_string'));
        $this->assertEquals('The min array must have at least 5 items.', $errors->first('min_array'));
        $this->assertEquals('The numeric must be a number.', $errors->first('numeric'));
        $this->assertEquals('The present field must be present.', $errors->first('present'));
        $this->assertEquals('The regex format is invalid.', $errors->first('regex'));
        $this->assertEquals('The required field is required.', $errors->first('required'));
        $this->assertEquals('The required if field is required when in is c.', $errors->first('required_if'));
        $this->assertEquals('The required with field is required when email / ip is present.', $errors->first('required_with'));
        $this->assertEquals('The required with all field is required when email / ip is present.', $errors->first('required_with_all'));
        $this->assertEquals('The required without field is required when filled / required is not present.', $errors->first('required_without'));
        $this->assertEquals('The required without all field is required when none of filled / required are present.', $errors->first('required_without_all'));
        $this->assertEquals('The same and date must match.', $errors->first('same'));
        $this->assertEquals('The size numeric must be 5.', $errors->first('size_numeric'));
        $this->assertEquals('The size string singular must be 1 character.', $errors->first('size_string_singular'));
        $this->assertEquals('The size file singular must be 1 kilobyte.', $errors->first('size_file_singular'));
        $this->assertEquals('The size array singular must contain 1 item.', $errors->first('size_array_singular'));
        $this->assertEquals('The size string plural must be 5 characters.', $errors->first('size_string_plural'));
        $this->assertEquals('The size file plural must be 5 kilobytes.', $errors->first('size_file_plural'));
        $this->assertEquals('The size array plural must contain 5 items.', $errors->first('size_array_plural'));
        $this->assertEquals('The string must be a string.', $errors->first('string'));
        $this->assertEquals('The timezone must be a valid zone.', $errors->first('timezone'));

        // Delete temporary images directory
        File::deleteDirectory($imgDir);
    }

}
