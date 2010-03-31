<?php
/**
 * @author Oliver Lillie (aka buggedcom) <publicmail@buggedcom.co.uk>
 *
 * @license BSD
 * @copyright Copyright (c) 2008 Oliver Lillie <http://www.buggedcom.co.uk>
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:  The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package PHPVideoToolkit (was called ffmpeg)
 * @version 0.1.5
 * @changelog SEE CHANGELOG
 * @abstract This class can be used in conjunction with several server binary libraries to manipulate video and audio
 * through PHP. It is not intended to solve any particular problems, however you may find it useful. This php class
 * is in no way associated with the actual FFmpeg releases. Any mistakes contained in this php class are mine and mine
 * alone.
 *
 * Please Note: There are several prerequisites that are required before this class can be used as an aid to manipulate
 * video and audio. You must at the very least have FFMPEG compiled on your server. If you wish to use this class for FLV
 * manipulation you must compile FFMPEG with LAME and Ruby's FLVTOOL2. I cannot answer questions regarding the install of
 * the server binaries needed by this class. I had too learn the hard way and it isn't easy, however it is a good learning
 * experience. For those of you who do need help read the install.txt file supplied along side this class. It wasn't written
 * by me however I found it useful when installing ffmpeg for the first time. The original source for the install.txt file
 * is located http://www.luar.com.hk/blog/?p=669 and the author is Lunar.
 *
 * @see install.txt
 *
 * @uses ffmpeg http://ffmpeg.sourceforge.net/
 * @uses lame http://lame.sourceforge.net/
 * @uses flvtool2 http://www.inlet-media.de/flvtool2 (and ruby http://www.ruby-lang.org/en/)
 */

/**
 * Set the ffmpeg binary path
 */
if (!defined('PHPVIDEOTOOLKIT_TEMP_DIRECTORY'))
{
    define('PHPVIDEOTOOLKIT_TEMP_DIRECTORY', '/tmp/');
}
/**
 * Set the ffmpeg binary path
 */
if (!defined('PHPVIDEOTOOLKIT_FFMPEG_BINARY'))
{
    define('PHPVIDEOTOOLKIT_FFMPEG_BINARY', '/usr/local/bin/ffmpeg');
}
/**
 * Set the flvtool2 binary path
 */
if (!defined('PHPVIDEOTOOLKIT_FLVTOOLS_BINARY'))
{
    define('PHPVIDEOTOOLKIT_FLVTOOLS_BINARY', '/usr/bin/flvtool2');
}

/**
 * Set the memcoder path
 */
if (!defined('PHPVIDEOTOOLKIT_MENCODER_BINARY'))
{
    define('PHPVIDEOTOOLKIT_MENCODER_BINARY', '/usr/local/bin/mencoder');
}

class net_sourceforge_phpvideotoolkit_PhpVideoToolkit
{
    
    public $version = '0.1.5';
    
    /**
     * Error strings
     */

    private $_messages = array(
		
		'generic_temp_404' 								=> 'The temporary directory does not exist.',
		'generic_temp_writable' 						=> 'The temporary directory is not write-able by the web server.',
		
		'getFileInfo_no_input' 							=> 'Input file does not exist so no information can be retrieved.',
		'streamFLV_no_input' 							=> 'Input file has not been set so the FLV cannot be streamed.',
		'streamFLV_passed_eof' 							=> 'You have tried to stream to a point in the file that does not exit.',
		'setInputFile_file_existence' 					=> 'Input file "#file" does not exist',
		'extractAudio_valid_format' 					=> 'Value "#format" set from $toolkit->extractAudio, is not a valid audio format. Valid values ffmpeg self::FORMAT_AAC, PHPVideoToolkit::FORMAT_AIFF, PHPVideoToolkit::FORMAT_MP2, PHPVideoToolkit::FORMAT_MP3, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG4, PHPVideoToolkit::FORMAT_M4A or PHPVideoToolkit::FORMAT_WAV. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-f" and $argument to your required value.',
		'extractFrame_video_frame_rate_404' 			=> 'You have attempted to extract a thumbnail from a video while automagically guessing the framerate of the video, but the framerate could not be accessed. You can remove this error by manually setting the frame rate of the video.',
		'extractFrame_video_info_404' 					=> 'You have attempted to extract a thumbnail from a video and check to see if the thumbnail exists, however it was not possible to access the video information. Please check your temporary directory permissions for read/write access by the webserver.',
		'extractFrame_video_frame_count' 				=> 'You have attempted to extract a thumbnail from a video but the thumbnail you are trying to extract does not exist in the video.',
		'extractFrames_video_begin_frame_count' 		=> 'You have attempted to extract thumbnails from a video but the thumbnail you are trying to start the extraction from does not exist in the video.',
		'extractFrames_video_end_frame_count' 			=> 'You have attempted to extract thumbnails from a video but the thumbnail you are trying to end the extraction at does not exist in the video.',
		'setFormat_valid_format' 						=> 'Value "#format" set from $toolkit->setFormat, is not a valid format. Valid values are PHPVideoToolkit::FORMAT_3GP2, PHPVideoToolkit::FORMAT_3GP, PHPVideoToolkit::FORMAT_AAC, PHPVideoToolkit::FORMAT_AIFF, PHPVideoToolkit::FORMAT_AMR, PHPVideoToolkit::FORMAT_ASF, PHPVideoToolkit::FORMAT_AVI, PHPVideoToolkit::FORMAT_FLV, PHPVideoToolkit::FORMAT_GIF, PHPVideoToolkit::FORMAT_MJ2, PHPVideoToolkit::FORMAT_MP2, PHPVideoToolkit::FORMAT_MP3, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG4, PHPVideoToolkit::FORMAT_M4A, PHPVideoToolkit::FORMAT_MPEG, PHPVideoToolkit::FORMAT_MPEG1, PHPVideoToolkit::FORMAT_MPEG2, PHPVideoToolkit::FORMAT_MPEGVIDEO, PHPVideoToolkit::FORMAT_PSP, PHPVideoToolkit::FORMAT_RM, PHPVideoToolkit::FORMAT_SWF, PHPVideoToolkit::FORMAT_VOB, PHPVideoToolkit::FORMAT_WAV, PHPVideoToolkit::FORMAT_JPG. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-f" and $argument to your required value.',
		'setAudioSampleFrequency_valid_frequency' 		=> 'Value "#frequency" set from $toolkit->setAudioSampleFrequency, is not a valid integer. Valid values are 11025, 22050, 44100. If you wish to specifically try to set another frequency you should use the advanced function $toolkit->addCommand. Set $command to "-ar" and $argument to your required value.',
		'setAudioFormat_valid_format' 					=> 'Value "#format" set from $toolkit->setAudioFormat, is not a valid format. Valid values are PHPVideoToolkit::FORMAT_AAC, PHPVideoToolkit::FORMAT_AIFF, PHPVideoToolkit::FORMAT_AMR, PHPVideoToolkit::FORMAT_ASF, PHPVideoToolkit::FORMAT_MP2, PHPVideoToolkit::FORMAT_MP3, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG2, PHPVideoToolkit::FORMAT_RM, PHPVideoToolkit::FORMAT_WAV. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-acodec" and $argument to your required value.',
		'setVideoFormat_valid_format' 					=> 'Value "#format" set from $toolkit->setAudioFormat, is not a valid format. Valid values are PHPVideoToolkit::FORMAT_3GP2, PHPVideoToolkit::FORMAT_3GP, PHPVideoToolkit::FORMAT_AVI, PHPVideoToolkit::FORMAT_FLV, PHPVideoToolkit::FORMAT_GIF, PHPVideoToolkit::FORMAT_MJ2, PHPVideoToolkit::FORMAT_MP4, PHPVideoToolkit::FORMAT_MPEG4, PHPVideoToolkit::FORMAT_M4A, PHPVideoToolkit::FORMAT_MPEG, PHPVideoToolkit::FORMAT_MPEG1, PHPVideoToolkit::FORMAT_MPEG2, PHPVideoToolkit::FORMAT_MPEGVIDEO. If you wish to specifically try to set another format you should use the advanced function $toolkit->addCommand. Set $command to "-vcodec" and $argument to your required value.',
		'setAudioBitRate_valid_bitrate' 				=> 'Value "#bitrate" set from $toolkit->setAudioBitRate, is not a valid integer. Valid values are 16, 32, 64, 128. If you wish to specifically try to set another bitrate you should use the advanced function $toolkit->addCommand. Set $command to "-ab" and $argument to your required value.',
		'prepareImagesForConversionToVideo_one_img' 	=> 'When compiling a movie from a series of images, you must include at least one image.',
		'prepareImagesForConversionToVideo_img_404' 	=> '"#img" does not exist.',
		'prepareImagesForConversionToVideo_img_copy' 	=> '"#img" can not be copied to "#tmpfile"',
		'prepareImagesForConversionToVideo_img_type' 	=> 'The images can not be prepared for conversion to video. Please make sure all images are of the same type, ie gif, png, jpeg and then try again.',
		'setVideoOutputDimensions_valid_format' 		=> 'Value "#format" set from $toolkit->setVideoOutputDimensions, is not a valid preset dimension. Valid values are PHPVideoToolkit::SIZE_SQCIF, PHPVideoToolkit::SIZE_SAS, PHPVideoToolkit::SIZE_QCIF, PHPVideoToolkit::SIZE_CIF, PHPVideoToolkit::SIZE_4CIF, PHPVideoToolkit::SIZE_QQVGA, PHPVideoToolkit::SIZE_QVGA, PHPVideoToolkit::SIZE_VGA, PHPVideoToolkit::SIZE_SVGA, PHPVideoToolkit::SIZE_XGA, PHPVideoToolkit::SIZE_UXGA, PHPVideoToolkit::SIZE_QXGA, PHPVideoToolkit::SIZE_SXGA, PHPVideoToolkit::SIZE_QSXGA, PHPVideoToolkit::SIZE_HSXGA, PHPVideoToolkit::SIZE_WVGA, PHPVideoToolkit::SIZE_WXGA, PHPVideoToolkit::SIZE_WSXGA, PHPVideoToolkit::SIZE_WUXGA, PHPVideoToolkit::SIZE_WOXGA, PHPVideoToolkit::SIZE_WQSXGA, PHPVideoToolkit::SIZE_WQUXGA, PHPVideoToolkit::SIZE_WHSXGA, PHPVideoToolkit::SIZE_WHUXGA, PHPVideoToolkit::SIZE_CGA, PHPVideoToolkit::SIZE_EGA, PHPVideoToolkit::SIZE_HD480, PHPVideoToolkit::SIZE_HD720, PHPVideoToolkit::SIZE_HD1080. You can also manually set the width and height.',
		'setVideoOutputDimensions_sas_dim' 				=> 'It was not possible to determine the input video dimensions so it was not possible to continue. If you wish to override this error please change the call to setVideoOutputDimensions and add a true argument to the arguments list... setVideoOutputDimensions(PHPVideoToolkit::SIZE_SAS, true);',
		'setVideoOutputDimensions_valid_integer' 		=> 'You tried to set the video output dimensions to an odd number. FFmpeg requires that the video output dimensions are of event value and divisible by 2. ie 2, 4, 6,... etc',
		'setVideoAspectRatio_valid_ratio' 				=> 'Value "#ratio" set from $toolkit->setVideoOutputDimensions, is not a valid preset dimension. Valid values are PHPVideoToolkit::RATIO_STANDARD, PHPVideoToolkit::RATIO_WIDE, PHPVideoToolkit::RATIO_CINEMATIC. If you wish to specifically try to set another video aspect ratio you should use the advanced function $toolkit->addCommand. Set $command to "-aspect" and $argument to your required value.',
		'addWatermark_img_404' 							=> 'Watermark file "#watermark" does not exist.',
		'addWatermark_vhook_disabled' 					=> 'Vhooking is not enabled in your FFmpeg binary. In order to allow video watermarking you must have FFmpeg compiled with --enable-vhook set. You can however watermark any extracted images using GD. To enable frame watermarking, call $toolkit->addGDWatermark($file) before you execute the extraction.',
		'addVideo_file_404' 							=> 'File "#file" does not exist.',
		'setOutput_output_dir_404' 						=> 'Output directory "#dir" does not exist!',
		'setOutput_output_dir_writable' 				=> 'Output directory "#dir" is not writable!',
		'setOutput_%_missing' 							=> 'The output of this command will be images yet you have not included the "%index" or "%timecode" in the $output_name.',
		'setOutput_%d_depreciated' 						=> 'The use of %d in the output file name is now depreciated. Please use %index. Number padding is still supported. You may also use %timecode instead to add a timecode to the filename.',
		'execute_input_404' 							=> 'Execute error. Input file missing.',
		'execute_output_not_set' 						=> 'Execute error. Output not set.',
		'execute_overwrite_process' 					=> 'Execute error. A file exists in the temp directory and is of the same name as this process file. It will conflict with this conversion. Conversion stopped.',
		'execute_overwrite_fail' 						=> 'Execute error. Output file exists. Process halted. If you wish to automatically overwrite files set the third argument in "PHPVideoToolkit::setOutput();" to "PHPVideoToolkit::OVERWRITE_EXISTING".',
		'execute_partial_error' 						=> 'Execute error. Output for file "#input" encountered a partial error. Files were generated, however one or more of them were empty.',
		'execute_image_error' 							=> 'Execute error. Output for file "#input" was not found. No images were generated.',
		'execute_output_404' 							=> 'Execute error. Output for file "#input" was not found. Please check server write permissions and/or available codecs compiled with FFmpeg. You can check the encode decode availability by inspecting the output array from PHPVideoToolkit::getFFmpegInfo().',
		'execute_output_empty' 							=> 'Execute error. Output for file "#input" was found, but the file contained no data. Please check the available codecs compiled with FFmpeg can support this type of conversion. You can check the encode decode availability by inspecting the output array from PHPVideoToolkit::getFFmpegInfo().',
		'execute_image_file_exists'						=> 'Execute error. There is a file name conflict. The file "#file" already exists in the filesystem. If you wish to automatically overwrite files set the third argument in "PHPVideoToolkit::setOutput();" to "PHPVideoToolkit::OVERWRITE_EXISTING".',
		'execute_result_ok_but_unwritable'				=> 'Process Partially Completed. The process successfully completed however it was not possible to output to "#output". The output was left in the temp directory "#process" for a manual file movement.',
		'execute_result_ok'								=> 'Process Completed. The process successfully completed. Output was generated to "#output".',
			
		'ffmpeg_log_ffmpeg_output'						=> 'OUTPUT',
		'ffmpeg_log_ffmpeg_result'						=> 'RESULT',
		'ffmpeg_log_ffmpeg_command'						=> 'COMMAND',
		'ffmpeg_log_ffmpeg_join_gunk'					=> 'FFMPEG JOIN OUTPUT',
		'ffmpeg_log_ffmpeg_gunk'						=> 'FFMPEG OUTPUT',
		'ffmpeg_log_separator'							=> '-------------------------------'
			
		);

    /** any return value with this means everything is ok */
    const RESULT_OK = true;
    
    /** 
     * any return value with this means the file has been processed/converted ok however it was 
     * not able to be written to the output address. If this occurs you will need to move the
     * processed file manually from the temp location
     */
    const RESULT_OK_BUT_UNWRITABLE     = -1;
        
    /**
     * Codec support constants
     */
    const ENCODE = 'encode';
    const DECODE = 'decode';
        
    /**
     * Overwrite constants used in setOutput
     */
    const OVERWRITE_FAIL        = 'fail';
    const OVERWRITE_PRESERVE    = 'preserve';
    const OVERWRITE_EXISTING    = 'existing';
    const OVERWRITE_UNIQUE        = 'unique';
    
    /**
     * Formats supported
     * 3g2             3gp2 format
     * 3gp             3gp format
     * aac             ADTS AAC
     * aiff            Audio IFF
     * amr             3gpp amr file format
     * asf             asf format
     * avi             avi format
     * flv             flv format
     * gif             GIF Animation
     * mov             mov format
     * mov,mp4,m4a,3gp,3g2,mj2 QuickTime/MPEG4/Motion JPEG 2000 format
     * mp2             MPEG audio layer 2
     * mp3             MPEG audio layer 3
     * mp4             mp4 format
     * mpeg            MPEG1 System format
     * mpeg1video      MPEG video
     * mpeg2video      MPEG2 video
     * mpegvideo       MPEG video
     * psp             psp mp4 format
     * rm              rm format
     * swf             Flash format
     * vob             MPEG2 PS format (VOB)
     * wav             wav format
     * jpeg            mjpeg format
     * yuv4mpegpipe    yuv4mpegpipe format
     */
    const FORMAT_3GP2     = '3g2';
    const FORMAT_3GP     = '3gp';
    const FORMAT_AAC    = 'aac';
    const FORMAT_AIFF     = 'aiff';
    const FORMAT_AMR     = 'amr';
    const FORMAT_ASF     = 'asf';
    const FORMAT_AVI    = 'avi';
    const FORMAT_FLV     = 'flv';
    const FORMAT_GIF     = 'gif';
    const FORMAT_MJ2     = 'mj2';
    const FORMAT_MP2     = 'mp2';
    const FORMAT_MP3     = 'mp3';
    const FORMAT_MP4     = 'mp4';
    const FORMAT_MPEG4     = 'mpeg4';
    const FORMAT_M4A     = 'm4a';
    const FORMAT_MPEG     = 'mpeg';
    const FORMAT_MPEG1     = 'mpeg1video';
    const FORMAT_MPEG2     = 'mpeg2video';
    const FORMAT_MPEGVIDEO     = 'mpegvideo';
    const FORMAT_PSP     = 'psp';
    const FORMAT_RM     = 'rm';
    const FORMAT_SWF     = 'swf';
    const FORMAT_VOB     = 'vob';
    const FORMAT_WAV     = 'wav';
    const FORMAT_JPG     = 'mjpeg';
    const FORMAT_Y4MP     = 'yuv4mpegpipe';
    
    /**
     * Size Presets
     */
    const SIZE_SAS         = 'SameAsSource';
    const SIZE_SQCIF     = '128x96';
    const SIZE_QCIF     = '176x144';
    const SIZE_CIF         = '352x288';
    const SIZE_4CIF     = '704x576';
    const SIZE_QQVGA     = '160x120';
    const SIZE_QVGA     = '320x240';
    const SIZE_VGA         = '640x480';
    const SIZE_SVGA     = '800x600';
    const SIZE_XGA         = '1024x768';
    const SIZE_UXGA     = '1600x1200';
    const SIZE_QXGA     = '2048x1536';
    const SIZE_SXGA     = '1280x1024';
    const SIZE_QSXGA     = '2560x2048';
    const SIZE_HSXGA     = '5120x4096';
    const SIZE_WVGA     = '852x480';
    const SIZE_WXGA     = '1366x768';
    const SIZE_WSXGA     = '1600x1024';
    const SIZE_WUXGA     = '1920x1200';
    const SIZE_WOXGA     = '2560x1600';
    const SIZE_WQSXGA     = '3200x2048';
    const SIZE_WQUXGA     = '3840x2400';
    const SIZE_WHSXGA     = '6400x4096';
    const SIZE_WHUXGA     = '7680x4800';
    const SIZE_CGA         = '320x200';
    const SIZE_EGA        = '640x350';
    const SIZE_HD480     = '852x480';
    const SIZE_HD720     = '1280x720';
    const SIZE_HD1080    = '1920x1080';
    
    /**
     * Ratio Presets
     */
    const RATIO_STANDARD    = '4:3';
    const RATIO_WIDE    = '16:9';
    const RATIO_CINEMATIC    = '1.85';
    
    /**
     * A public var that is to the information available about
     * the current ffmpeg compiled binary.
     * @var mixed
     * @access public
     */
    public static $ffmpeg_info = false;

    /**
     * A private var that contains the info of any file that is accessed by net_sourceforge_phpvideotoolkit_PhpVideoToolkit::getFileInfo();
     * @var array
     * @access private
     */
    private static $_file_info = array();

    /**
     * Determines what happens when an error occurs
     * @var boolean If true then the script will die, if not false is return by the error
     * @access public
     */
    public $on_error_die = false;

    /**
     * Holds the log file name
     * @var string
     * @access private
     */
    private $_log_file = null;

    /**
     * Determines if when outputting image frames if the outputted files should have the %d number
     * replaced with the frames timecode.
     * @var boolean If true then the files will be renamed.
     * @access public
     */
    public $image_output_timecode = true;

    /**
     * Holds the timecode separator for when using $image_output_timecode = true
     * Not all systems allow ':' in filenames.
     * @var string
     * @access public
     */
    public $timecode_seperator_output = '-';

    /**
     * Holds the starting time code when outputting image frames.
     * @var string The timecode hh(n):mm:ss:ff
     * @access private
     */
    private $_image_output_timecode_start = '00:00:00.00';

    /**
     * The format in which the image %timecode placeholder string is outputted.
     *     - %hh (hours) representative of hours
     *     - %mm (minutes) representative of minutes
     *     - %ss (seconds) representative of seconds
     *     - %fn (frame number) representative of frames (of the current second, not total frames)
     *     - %ms (milliseconds) representative of milliseconds (of the current second, not total milliseconds) (rounded to 3 decimal places)
     *     - %ft (frames total) representative of total frames (ie frame number)
     *     - %st (seconds total) representative of total seconds (rounded).
     *     - %sf (seconds floored) representative of total seconds (floored).
     *     - %mt (milliseconds total) representative of total milliseconds. (rounded to 3 decimal places)
     * NOTE; there are special characters that will be replace by net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$timecode_seperator_output, these characters are
     *     - :
     *  - .
     * @var string 
     * @access public
     */
    private $image_output_timecode_format = '%hh-%mm-%ss-%fn';

    /**
     * Holds the fps of image extracts
     * @var integer
     * @access private
     */
    private $_image_output_timecode_fps = 1;

    /**
     * Holds the current execute commands that will need to be combined
     * @var array
     * @access private
     */
    private $_commands = array();

    /**
     * Holds the commands executed
     * @var array
     * @access private
     */
    private $_processed = array();

    /**
     * Holds the file references to those that have been processed
     * @var array
     * @access private
     */
    private $_files    = array();

    /**
     * Holds the errors encountered
     * @access private
     * @var array
     */
    private $_errors = array();

    /**
     * Holds the input file / input file sequence
     * @access private
     * @var string
     */
    private $_input_file = null;

    /**
     * Holds the output file / output file sequence
     * @access private
     * @var string
     */
    private $_output_address = null;

    /**
     * Holds the process file / process file sequence
     * @access private
     * @var string
     */
    private $_process_address = null;

    /**
     * Temporary filename prefix
     * @access private
     * @var string
     */
    private $_tmp_file_prefix = 'tmp_';

    /**
     * Holds the temporary directory name
     * @access private
     * @var string
     */
    private $_tmp_directory = null;

    /**
     * Holds the directory paths that need to be removed by the ___destruct function
     * @access private
     * @var array
     */
    private $_unlink_dirs = array();

    /**
     * Holds the file paths that need to be deleted by the ___destruct function
     * @access private
     * @var array
     */
    private $_unlink_files = array();

    /**
     * Holds the timer start micro-float.
     * @access private
     * @var integer
     */
    private $_timer_start = 0;

    /**
     * Holds the times taken to process each file.
     * @access private
     * @var array
     */
    private $_timer = array();

    /**
     * Holds the times taken to process each file.
     * @access private
     * @var constant
     */
    private $_overwrite_mode = null;

    /**
     * Holds a integer value that flags if the image extraction is just a single frame.
     * @access private
     * @var integer
     */
    private $_single_frame_extraction = null;

    /**
     * Holds the watermark file that is used to watermark any outputted images via GD.
     * @access private
     * @var string
     */
    private $_watermark_url    = null;

    /**
     * Holds the watermark options used to watermark any outputted images via GD.
     * @access private
     * @var array
     */
    private $_watermark_options = null;

    /**
     * Holds the number of files processed per run.
     * @access private
     * @var integer
     */
    private $_process_file_count = 0;

    /**
     * Holds the times taken to process each file.
     * @access private
     * @var array
     */
    private $_post_processes = array();

    /**
     * Holds the times taken to process each file.
     * @access private
     * @var array
     */
    private static $_static    = true;

    /**
     * Holds commands should be sent added to the exec before the input file, this is by no means a definitive list
     * of all the ffmpeg commands, as it only utilizes the ones in use by this class. Also only commands that have 
     * specific required places are entered in the arrays below. Anything not in these arrays will be treated as an 
     * after-input item.
     * @access private
     * @var array
     */
    private $_cmds_before_input = array('-inputr');

    /**
     * Constructs the class and sets the temporary directory.
     *
     * @access private
     * @param string $tmp_directory A full absolute path to you temporary directory
     */
    function __construct($tmp_directory='/tmp/')
    {
        $this->_tmp_directory = $tmp_directory;
        net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$_static = false;
    }

    public static function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    /**
     * Resets the class
     *
     * @access public
     * @param boolean $keep_input_file Determines whether or not to reset the input file currently set.
     */
    public function reset($keep_input_file = false, $keep_processes = false)
    {
        if ($keep_input_file === false) {
            $this->_input_file = null;
        }
        if ($keep_processes === false) {
            $this->_post_processes = array();
        }
        $this->_single_frame_extraction = null;
        $this->_output_address = null;
        $this->_process_address = null;
        $this->_log_file = null;
        $this->_commands = array();
        $this->_timer_start = 0;
        $this->_process_file_count = 0;
        $this->__destruct();
    }

    /**
     * Returns information about the specified file without having to use ffmpeg-php
     * as it consults the ffmpeg binary directly. 
     * 
     * @access public
     * @param string $tmp_directory A full absolute path to you temporary directory. Only needed in a static call.
     * @return mixed false on error encountered, true otherwise
     **/
    public function getFFmpegInfo($tmp_directory='/tmp/')
    {
        /* check to see if the info has already been cached */
        if (net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$ffmpeg_info !== false) {
            return net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$ffmpeg_info;
        }

        /* check to see if this is a static call */
        if (net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$_static === true) {     
            $toolkit = new net_sourceforge_phpvideotoolkit_PhpVideoToolkit($tmp_directory);
            return $toolkit->getFFmpegInfo();
        }
        $format = '';

        /* execute the ffmpeg lookup */
        exec(PHPVIDEOTOOLKIT_FFMPEG_BINARY.' -formats 2>&1', $buffer);
        $buffer = implode("\r\n", $buffer);
        $data = array();
        $data['compiler'] = array();
        $look_ups = array(
            'configuration'    =>'configuration: ',
            'formats'    =>'File formats:', 
            'codecs'    =>'Codecs:', 
            'filters'    =>'Bitstream filters:', 
            'protocols'    =>'Supported file protocols:', 
            'abbreviations'    =>'Frame size, frame rate abbreviations:', 'Note:');
        $total_lookups = count($look_ups);
        $pregs = array();
        $indexs = array();
        foreach($look_ups as $key=>$reg) {
            if (strpos($buffer, $reg) !== false) {
                $index = array_push($pregs, $reg);
                $indexs[$key] = $index;
            }
        }
        preg_match('/'.implode('(.*)', $pregs).'/s', $buffer, $matches);
        $configuration = trim($matches[$indexs['configuration']]);

         /* grab the ffmpeg configuration flags */
        preg_match_all('/--[a-zA-Z0-9\-]+/', $configuration, $config_flags);
        $data['compiler']['configuration'] = $config_flags[0];
        $data['compiler']['vhook-support'] = in_array('--enable-vhook', $config_flags[0]) && !in_array('--disable-vhook', $config_flags[0]);
        
        /* grab the versions */
        $data['compiler']['versions'] = array();
        preg_match_all('/([a-zA-Z0-9\-]+) version: ([0-9\.]+)/', $configuration, $versions);
        for($i=0, $a=count($versions[0]); $i<$a; $i++) {
            $data['compiler']['versions'][strtolower(trim($versions[1][$i]))] = $versions[2][$i];
        }

        /* grab the ffmpeg compile info */
        preg_match('/built on (.*), gcc: (.*)/', $configuration, $conf);
        if (count($conf) > 0) {
            $data['compiler']['gcc'] = $conf[2];
            $data['compiler']['build_date'] = $conf[1];
            $data['compiler']['build_date_timestamp'] = strtotime($conf[1]);
        }

        /* grab the file formats available to ffmpeg */
        preg_match_all('/ (DE|D|E) (.*) {1,} (.*)/', trim($matches[$indexs['formats']]), $formats);
        $data['formats'] = array();

        /* loop and clean */
        for($i=0, $a=count($formats[0]); $i<$a; $i++) {
            $data['formats'][strtolower(trim($formats[2][$i]))] = array(
                'encode'     => $formats[1][$i] == 'DE' || $formats[1][$i] == 'E',
                'decode'     => $formats[1][$i] == 'DE' || $formats[1][$i] == 'D',
                'fullname'    => $formats[3][$i]
            );
        }

        /* grab the bitstream filters available to ffmpeg */
        $data['filters'] = array();
        if (isset($indexs['filters']) && isset($matches[$indexs['filters']])) {
            $filters = trim($matches[$indexs['filters']]);
            if (empty($filters)) {
                $data['filters'] = explode(' ', $filters);
            }
        }

        /* grab the file prototcols available to ffmpeg */
        $data['filters'] = array();
        if (isset($indexs['protocols']) && isset($matches[$indexs['protocols']])) {
            $protocols = trim($matches[$indexs['protocols']]);
            if (empty($protocols)) {
                $data['protocols'] = explode(' ', str_replace(':', '', $protocols));
            }
        }

        /* grab the abbreviations available to ffmpeg */
        $data['abbreviations'] = array();
        if (isset($indexs['abbreviations']) && isset($matches[$indexs['abbreviations']])) {
        $abbreviations = trim($matches[$indexs['abbreviations']]);
        if (empty($abbreviations)) {
            $data['abbreviations'] = explode(' ', $abbreviations);
        }
    
        net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$ffmpeg_info = $data;
        $data['ffmpeg-php-support'] = $this->hasFFmpegPHPSupport();
        return $data;
    }
        
    /**
     * Determines if your ffmpeg has particular codec support for encode or decode.
     * 
     * @access public
     * @param string $codec The name of the codec you are checking for. 
     * @param const $support net_sourceforge_phpvideotoolkit_PhpVideoToolkit::ENCODE or net_sourceforge_phpvideotoolkit_PhpVideoToolkit::DECODE, depending on which functionality is desired.
     * @return mixed. Boolean false if there is no support, true if there is support.
     */
    public function hasCodecSupport($codec, $support=net_sourceforge_phpvideotoolkit_PhpVideoToolkit::ENCODE)
    {      
        $codec = strtolower($codec);
        $data = $this->getFFmpegInfo();     
        return isset($data['formats'][$codec]) ? $data['formats'][$codec][$support] : false;
    }
        
    /**
     * Determines the type of support that exists for the FFmpeg-PHP module.
     * 
     * @access public
     * @return mixed. Boolean false if there is no support, String 'module' if the actuall
     *         FFmpeg-PHP module is loaded, or String 'emulated' if the FFmpeg-PHP classes
     *         can be emulated through the adapter classes.
     */
    public function hasFFmpegPHPSupport()
    {
        return extension_loaded('ffmpeg') ? 'module' : (is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'adapters'.DIRECTORY_SEPARATOR.'ffmpeg-php'.DIRECTORY_SEPARATOR.'ffmpeg_movie.php') && is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'adapters'.DIRECTORY_SEPARATOR.'ffmpeg-php'.DIRECTORY_SEPARATOR.'ffmpeg_frame.php') && is_file(dirname(__FILE__).DIRECTORY_SEPARATOR.'adapters'.DIRECTORY_SEPARATOR.'ffmpeg-php'.DIRECTORY_SEPARATOR.'ffmpeg_animated_gif.php') ? 'emulated' : false);
    }
        
    /**
     * Determines if the ffmpeg binary has been compiled with vhook support.
     * 
     * @access public
     * @return mixed. Boolean false if there is no support, true there is support.
     */
    public function hasVHookSupport()
    {
        $info = $this->getFFmpegInfo();
        return $info['compiler']['vhook-support'];
    }
        
    /**
     * Returns information about the specified file without having to use ffmpeg-php
     * as it consults the ffmpeg binary directly. This idea for this function has been borrowed from
     * a French ffmpeg class located: http://www.phpcs.com/codesource.aspx?ID=45279
     * 
     * @access public
     * @todo Change the search from string explode to a regex based search
     * @param string $file The absolute path of the file that is required to be manipulated.
     * @return mixed false on error encountered, true otherwise
     **/
    public function getFileInfo($file=false, $tmp_directory='/tmp/')
    {
        /* check to see if this is a static call */
        if ($file !== false && net_sourceforge_phpvideotoolkit_PhpVideoToolkit::$_static === true) {     
            $toolkit = new net_sourceforge_phpvideotoolkit_PhpVideoToolkit($tmp_directory);
            return $toolkit->getFileInfo($file);
        }
        
        /* if the file has not been specified check to see if an input file has been specified */
        if ($file === false) {
            if (!$this->_input_file) {
                /* input file not valid */
                return $this->_raiseError('getFileInfo_no_input');
            }
            $file = $this->_input_file;
        }
        $file = escapeshellarg($file);

        /* create a hash of the filename */
        $hash = md5($file);
        
        /* check to see if the info has already been generated */
        if (isset(self::$_file_info[$hash])) {
            return self::$_file_info[$hash];
        }

        /* generate a random filename */
        $info_file = $this->_tmp_directory.$this->unique($hash).'.info';
            
        /* execute the ffmpeg lookup */
        exec(PHPVIDEOTOOLKIT_FFMPEG_BINARY.' -i '.$file.' 2>&1', $buffer);
        $buffer = implode("\r\n", $buffer);    
        $data = array();

        preg_match_all('/Duration: (.*)/', $buffer, $matches);
        if (count($matches) > 0) {
            $parts                             = explode(', ', trim($matches[1][0]));
            $data['duration']                    = array();
            $timecode                         = $parts[0];
            $data['duration']['seconds']                 = $this->timecodeToSeconds($timecode);
            $data['bitrate']                      = intval(ltrim($parts[2], 'bitrate: '));
            $data['duration']['start']                  = ltrim($parts[1], 'start: ');
            $data['duration']['timecode']                = array();
            $data['duration']['timecode']['rounded']         = substr($timecode, 0, 8);
            $data['duration']['timecode']['seconds']         = array();
            $data['duration']['timecode']['seconds']['exact']    = $timecode;
            $data['duration']['timecode']['seconds']['excess']      = intval(substr($timecode, 9));
        }
                
        /* match the video stream info */
        preg_match('/Stream(.*): Video: (.*)/', $buffer, $matches);
        if (count($matches) > 0) {
            $data['video'] = array();
            
            /* get the dimension parts */
            preg_match('/([0-9]{1,5})x([0-9]{1,5})/', $matches[2], $dimensions_matches);
            $dimensions_value = $dimensions_matches[0];
            $data['video']['dimensions'] = array(
                'width'     => floatval($dimensions_matches[1]),
                'height'    => floatval($dimensions_matches[2])
            );
            
            /* get the framerate */
            preg_match('/([0-9\.]+) (fps|tb)\(r\)/', $matches[0], $fps_matches);
            $data['video']['frame_rate']     = floatval($fps_matches[1]);
            $fps_value = $fps_matches[0];
             
            /* get the ratios */
            preg_match('/\[PAR ([0-9\:\.]+) DAR ([0-9\:\.]+)\]/', $matches[0], $ratio_matches);
            if (count($ratio_matches)) {
                $data['video']['pixel_aspect_ratio']     = $ratio_matches[1];
                $data['video']['display_aspect_ratio']     = $ratio_matches[2];
            }

             /* work out the number of frames */
            if (isset($data['duration']) && isset($data['video'])) {

                /* set the total frame count for the video */
                $data['video']['frame_count'] = ceil($data['duration']['seconds'] * $data['video']['frame_rate']);
                
                /* set the framecode */
                $frames    = ceil($data['video']['frame_rate']*($data['duration']['timecode']['seconds']['excess']/10));
                $data['duration']['timecode']['frames']         = array();
                $data['duration']['timecode']['frames']['exact']      = $data['duration']['timecode']['rounded'].'.'.$frames;
                $data['duration']['timecode']['frames']['excess']     = $frames;
                $data['duration']['timecode']['frames']['total']     = $data['video']['frame_count'];
            }
                     
            /* formats should be anything left over, let me know if anything else exists */
            $parts = explode(',', $matches[2]);
            $other_parts = array($dimensions_value, $fps_value);
            $formats = array();
            foreach($parts as $key=>$part) {
                $part = trim($part);
                if (!in_array($part, $other_parts)) {
                    array_push($formats, $part);
                }
            }
            $data['video']['pixel_format']     = $formats[1];
            $data['video']['codec']         = $formats[0];
        }
                
        /* match the audio stream info */
        preg_match('/Stream(.*): Audio: (.*)/', $buffer, $matches);
        if (count($matches) > 0) {
            /* setup audio values */
            $data['audio'] = array(
                'stereo'    => -1, 
                'sample_rate'    => -1, 
                'sample_rate'    => -1
            );
            $other_parts = array();
            
            /* get the stereo value */
            preg_match('/(stereo|mono)/i', $matches[0], $stereo_matches);
            if (count($stereo_matches)) {
                $data['audio']['stereo'] = $stereo_matches[0];
                array_push($other_parts, $stereo_matches[0]);
            }

            /* get the sample_rate */
            preg_match('/([0-9]{3,6}) Hz/', $matches[0], $sample_matches);
            if (count($sample_matches)) {
                $data['audio']['sample_rate']     = count($sample_matches) ? floatval($sample_matches[1]) : -1;
                array_push($other_parts, $sample_matches[0]);
            }

            /* get the bit rate */
            preg_match('/([0-9]{1,3}) kb\/s/', $matches[0], $bitrate_matches);
            if (count($bitrate_matches)) {
                $data['audio']['bitrate']         = count($bitrate_matches) ? floatval($bitrate_matches[1]) : -1;
                array_push($other_parts, $bitrate_matches[0]);
            }
        
            /* formats should be anything left over, let me know if anything else exists */
            $parts = explode(',', $matches[2]);
            $formats = array();
            foreach($parts as $key=>$part) {
                $part = trim($part);
                if (!in_array($part, $other_parts)) {
                    array_push($formats, $part);
                }
            }
            $data['audio']['codec']         = $formats[0];
        }

         /* check that some data has been obtained */
        if (!count($data)) {
            $data = false;
        } else {
            $data['_raw_info'] = $buffer;
        }
        
        return self::$_file_info[$hash] = $data;
    }        

    /**
     * Sets the input file that is going to be manipulated.
     *
     * @access public
     * @param string $file The absolute path of the file that is required to be manipulated.
     * @param mixed $input_frame_rate If 0 (default) then no input frame rate is set, if false it is automatically retreived, otherwise
     *         any other integer will be set as the incoming frame rate.
     * @return boolean false on error encountered, true otherwise
     */
    public function setInputFile($file, $input_frame_rate=0)
    {
        $files_length = count($file);

         /* if the total number of files entered is 1 then only one file is being processed */
        if ($files_length == 1) {

            /* check the input file, if there is a %d in there or a similar %03d then the file inputted is a sequence, if neither of those is found */
            /* then qheck to see if the file exists */
            if (!preg_match('/\%([0-9]+)d/', $file) && strpos($file, '%d') === false && !is_file($file)) {
                return $this->_raiseError('setInputFile_file_existence', array('file'=>$file));
            }
            $escaped_name = $file;
            $this->_input_file = $escaped_name;
            $this->_input_file_id = md5($escaped_name);
                
            /* the -inputr is a hack for -r to come before the input */
            if ($input_frame_rate !== 0) {
                $info = $this->getFileInfo();
                if (isset($info['video'])) {
                    if ($input_frame_rate === false) {
                        $input_frame_rate = $info['video']['frame_rate'];
                    }
                    /* input frame rate is a command hack */
                    $this->addCommand('-inputr', $input_frame_rate);
                }
            }
        } else {
            /* more than one video is being added as input so we must join them all */
            call_user_func_array(array(&$this, 'addVideo'), array($file, $input_frame_rate));
        }

        return true;
    }

    /**
     * @access public
     * @depreciated 
     * @see net_sourceforge_phpvideotoolkit_PhpVideoToolkit::setVideoCodec()
     */
    public function setVideoFormat($video_format)
    {
        return $this->setVideoCodec($video_format);
    }

    /**
     * Sets the video format for video outputs. This should not be confused with setFormat. setVideoFormat does not generally need to
     * be called unless setting a specific video format for a type of media format. It gets a little confusing...
     *
     * @access public
     * @param integer $video_format Valid values are 11025, 22050, 44100
     * @return boolean false on error encountered, true otherwise
     */
    public function setVideoCodec($video_format)
    {

        /* validate input */
        if (!in_array($video_format, array(self::FORMAT_3GP2, self::FORMAT_3GP, self::FORMAT_AVI, self::FORMAT_FLV, self::FORMAT_GIF, self::FORMAT_MJ2, self::FORMAT_MP4, self::FORMAT_MPEG4, self::FORMAT_M4A, self::FORMAT_MPEG, self::FORMAT_MPEG1, self::FORMAT_MPEG2, self::FORMAT_MPEGVIDEO))) {
            return $this->_raiseError('setVideoFormat_valid_format', array('format'=>$video_format));
        }

        return $this->addCommand('-vcodec', $video_format);
    }

    /**
     * @access public
     * @depreciated 
     * @see net_sourceforge_phpvideotoolkit_PhpVideoToolkit::setVideoDimensions()
     */
    public function setVideoOutputDimensions($width, $height=null)
    {
        return $this->setVideoDimensions($width, $height);
    }
        
    /**
     * Sets the video output dimensions (in pixels)
     *
     * @access public
     * @param mixed $width If an integer height also has to be specified, otherwise you can use one of the class constants
     * @param integer $height
     * @return boolean
     */
    public function setVideoDimensions($width, $height=null)
    {
        if ($height === null || $height === true) {

            /* validate input */
            if (!in_array($width, array(self::SIZE_SAS, self::SIZE_SQCIF, self::SIZE_QCIF, self::SIZE_CIF, self::SIZE_4CIF, self::SIZE_QQVGA, self::SIZE_QVGA, self::SIZE_VGA, self::SIZE_SVGA, self::SIZE_XGA, self::SIZE_UXGA, self::SIZE_QXGA, self::SIZE_SXGA, self::SIZE_QSXGA, self::SIZE_HSXGA, self::SIZE_WVGA, self::SIZE_WXGA, self::SIZE_WSXGA, self::SIZE_WUXGA, self::SIZE_WOXGA, self::SIZE_WQSXGA, self::SIZE_WQUXGA, self::SIZE_WHSXGA, self::SIZE_WHUXGA, self::SIZE_CGA, self::SIZE_EGA, self::SIZE_HD480, self::SIZE_HD720, self::SIZE_HD1080))) {
                return $this->_raiseError('setVideoOutputDimensions_valid_format', array('format'=>$format));
            }
            if ($width === self::SIZE_SAS) {

                /* an override is made so no command is added in the hope that ffmpeg will just output the source */
                if ($height === true) {
                    return true;
                }

                /* get the file info */
                $info = $this->getFileInfo();
                if (!isset($info['video']) || !isset($info['video']['dimensions'])) {
                    return $this->_raiseError('setVideoOutputDimensions_sas_dim');
                } else {
                    $width = $info['video']['dimensions']['width'].'x'.$info['video']['dimensions']['height'];
                }
            }
        } else {

            /* check that the width and height are even */
            if ($width % 2 !== 0 || $height % 2 !== 0) {
                return $this->_raiseError('setVideoOutputDimensions_valid_integer');
            }
            $width = $width.'x'.$height;
        }
        $this->addCommand('-s', $width);
        return true;
    }

    /**
     * Sets the video aspect ratio
     *
     * @access public
     * @param string|integer $ratio Valid values are net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RATIO_STANDARD, net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RATIO_WIDE, net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RATIO_CINEMATIC, or '4:3', '16:9', '1.85' 
     * @return boolean
     */
    public function setVideoAspectRatio($ratio)
    {
        if (!in_array($ratio, array(self::RATIO_STANDARD, self::RATIO_WIDE, self::RATIO_CINEMATIC))) {
            return $this->_raiseError('setVideoAspectRatio_valid_ratio', array('ratio'=>$ratio));
        }
        $this->addCommand('-aspect', $ratio);
        return true;
    }
        
    /**
     * Extracts exactly one frame
     *
     * @access public
     * @uses $toolkit->extractFrames
     * @param string $frame_timecode A timecode (hh:mm:ss.fn) where fn is the frame number of that second
     * @param integer|boolean $frames_per_second The frame rate of the movie. If left as the default, false. We will use net_sourceforge_phpvideotoolkit_PhpVideoToolkit::getFileInfo() to get
     *             the actual frame rate. It is recommended that it is left as false because an incorrect frame rate may produce unexpected results.
     * @param integer $timecode_format The format of the $extract_begin_timecode and $extract_end_timecode timecodes are being given in.
     *         default '%hh:%mm:%ss'
     *             - %hh (hours) representative of hours
     *             - %mm (minutes) representative of minutes
     *             - %ss (seconds) representative of seconds
     *             - %fn (frame number) representative of frames (of the current second, not total frames)
     *             - %ms (milliseconds) representative of milliseconds (of the current second, not total milliseconds) (rounded to 3 decimal places)
     *             - %ft (frames total) representative of total frames (ie frame number)
     *             - %st (seconds total) representative of total seconds (rounded).
     *             - %sf (seconds floored) representative of total seconds (floored).
     *             - %mt (milliseconds total) representative of total milliseconds. (rounded to 3 decimal places)
     *         Thus you could use an alternative, '%hh:%mm:%ss:%ms', or '%hh:%mm:%ss' dependent on your usage.
     * @param boolean $check_frame_exists Makes an explicit check to see if the frame exists, default = true. 
     *         Thanks to Istvan Szakacs for suggesting this check. Note, to improve performance disable this check.
     */
    public function extractFrame($frame_timecode, $frames_per_second=false, $frame_timecode_format='%hh:%mm:%ss.%fn', $check_frame_exists=true)
    {

        /* get the file info, will exit if no input has been set */
        if ($check_frame_exists || $frames_per_second === false) {
            $info = $this->getFileInfo();
            if ($info === false || !isset($info['video'])) {

                /* the input has not returned any video data so the frame rate can not be guessed */
                return $this->_raiseError('extractFrame_video_info_404');
            }
        }

        /* are we autoguessing the frame rate? */
        if ($frames_per_second === false) {
            if (!isset($info['video']['frame_rate'])) {
                /* the input has not returned any video data so the frame rate can not be guessed */
                return $this->_raiseError('extractFrame_video_frame_rate_404');
            }
            $frames_per_second = $info['video']['frame_rate'];
        }

        /* check if frame exists */
        if ($check_frame_exists) {
            if ($info['video']['frame_count'] < $this->formatTimecode($frame_timecode, $frame_timecode_format, '%ft', $frames_per_second)) {
                 /* the input has not returned any video data so the frame rate can not be guessed */
                return $this->_raiseError('extractFrame_video_frame_count');
            }
        }

         /* format the frame details if the timecode format is not already ok. */
        if ($frame_timecode_format !== '%hh:%mm:%ss.%ms') {
            $frame_timecode = $this->formatTimecode($frame_timecode, $frame_timecode_format, '%hh:%mm:%ss.%ms', $frames_per_second);
        }
        $this->_single_frame_extraction = 1;

         /**
         * we will limit the number of frames produced so the desired frame is the last image
         * this way we limit the cpu usage of ffmpeg
         * Thanks to Istvan Szakacs for pointing out that ffmpeg can export frames using the -ss hh:mm:ss[.xxx]
         * it has saved a lot of cpu intensive processes.
         */
        $this->extractFrames($frame_timecode, $frame_timecode, $frames_per_second, 1, '%hh:%mm:%ss.%ms', false);
             
        /* register the post tidy process */
         $this->registerPostProcess('_extractFrameTidy', $this);
    }

    /**
     * Sets the output.
     *
     * @access public
     * @param string $output_directory The directory to output the command output to
     * @param string $output_name The filename to output to.
     *             (Note; if you are outputting frames from a video then you will need to add an extra item to the output_name. The output name you set is required
     *             to contain '%d'. '%d' is replaced by the image number. Thus entering setting output_name $output_name='img%d.jpg' will output
     *             'img1.jpg', 'img2.jpg', etc... However 'img%03d.jpg' generates `img001.jpg', `img002.jpg', etc...)
     * @param boolean $overwrite_mode Accepts one of the following class constants
     *     - net_sourceforge_phpvideotoolkit_PhpVideoToolkit::OVERWRITE_FAIL        - This produces an error if there is a file conflict and the processing is halted.
     *     - net_sourceforge_phpvideotoolkit_PhpVideoToolkit::OVERWRITE_PRESERVE    - This continues with the processing but no file overwrite takes place. The processed file is left in the temp directory
     *                                       for you to manually move.
     *     - net_sourceforge_phpvideotoolkit_PhpVideoToolkit::OVERWRITE_EXISTING    - This will replace any existing files with the freshly processed ones.
     *     - net_sourceforge_phpvideotoolkit_PhpVideoToolkit::OVERWRITE_UNIQUE        - This will appended every output with a unique hash so that the filesystem is preserved.
     * @return boolean false on error encountered, true otherwise
     */
    public function setOutput($output_directory, $output_name, $overwrite_mode=net_sourceforge_phpvideotoolkit_PhpVideoToolkit::OVERWRITE_FAIL)
    {

        /* check if directoy exists */
        if (!is_dir($output_directory)) {
            return $this->_raiseError('setOutput_output_dir_404', array('dir'=>$output_directory));
        }

        /* check if directory is writeable */
        if (!is_writable($output_directory)) {
            return $this->_raiseError('setOutput_output_dir_writable', array('dir'=>$output_directory));
        }
        $process_name = '';

        /* check to see if a output delimiter is set */
        $has_d = preg_match('/\%([0-9]+)d/', $output_name) || strpos($output_name, '%d') !== false;
        if ($has_d) {
            return $this->_raiseError('setOutput_%d_depreciated');
        } else {

            /* determine if the extension is an image. If it is then we will be extracting frames so check for %d */
            $output_name_info = pathinfo($output_name);
            $is_image = in_array(strtolower($output_name_info['extension']), array('jpg', 'jpeg', 'png'));
            $is_gif      = strtolower($output_name_info['extension']) === 'gif';

            /* NOTE: for now we'll just stick to the common image formats, SUBNOTE: gif is ignore because ffmpeg can create animated gifs */
            if ($this->_single_frame_extraction !== null && strpos($output_name, '%timecode') !== false && !(preg_match('/\%index/', $output_name) || strpos($output_name, '%index') !== false) && $is_image) {
                return $this->_raiseError('setOutput_%_missing');
            }
            $process_name = '.'.$output_name_info['extension'];
                
            if ($is_image || ($this->_single_frame_extraction !== null && $is_gif)) {
                $process_name = '-%12d'.$process_name;
            }
        }

        /* set the output address */
        $this->_output_address = $output_directory.$output_name;

        /* set the processing address in the temp folder so it does not conflict with any other conversions */
        $this->_process_address = $this->_tmp_directory.$this->unique().$process_name;
        $this->_overwrite_mode = $overwrite_mode;
        return true;
    }
        
    /**
     * Sets a constant quality value to the encoding. (but a variable bitrate)
     * 
     * @param integer $quality The quality to adhere to. 100 is highest quality, 1 is the lowest quality
     */
    public function setConstantQuality($quality)
    {

        /* interpret quality into ffmpeg value */
        $quality = 31 - round(($quality/100) * 31);
        if ($quality > 31) {
            $quality = 31;
        } else if ($quality < 1) {
            $quality = 1;
        }
        $this->addCommand('-qscale', $quality);
    }

    /**
     * Translates a number of seconds to a timecode.
     * NOTE: this is now a depreciated, use formatSeconds() instead.
     *
     * @depreciated Use formatSeconds() instead.
     * @access public
     * @uses net_sourceforge_phpvideotoolkit_PhpVideoToolkit::formatSeconds()
     * @param integer $input_seconds The number of seconds you want to calculate the timecode for.
     */
    public function secondsToTimecode($input_seconds=0)
    {
        return $this->formatSeconds($input_seconds, '%hh:%mm:%ss');
    }

    /**
     * Translates a timecode to the number of seconds.
     * NOTE: this is now a depreciated, use formatTimecode() instead.
     *
     * @depreciated Use formatTimecode() instead.
     * @access public
     * @uses net_sourceforge_phpvideotoolkit_PhpVideoToolkit::formatTimecode()
     * @param integer $input_seconds The number of seconds you want to calculate the timecode for.
     */
    public function timecodeToSeconds($input_timecode='00:00:00')
    {
        return $this->formatTimecode($input_timecode, '%hh:%mm:%ss', '%st');
    }
        
    /**
     * Translates a number of seconds to a timecode.
     *
     * @access public
     * @param integer $input_seconds The number of seconds you want to calculate the timecode for.
     * @param integer $return_format The format of the timecode to return. The default is
     *         default '%hh:%mm:%ss'
     *             - %hh (hours) representative of hours
     *             - %mm (minutes) representative of minutes
     *             - %ss (seconds) representative of seconds
     *             - %fn (frame number) representative of frames (of the current second, not total frames)
     *             - %ms (milliseconds) representative of milliseconds (of the current second, not total milliseconds) (rounded to 3 decimal places)
     *             - %ft (frames total) representative of total frames (ie frame number)
     *             - %st (seconds total) representative of total seconds (rounded).
     *             - %sf (seconds floored) representative of total seconds (floored).
     *             - %sc (seconds ceiled) representative of total seconds (ceiled).
     *             - %mt (milliseconds total) representative of total milliseconds. (rounded to 3 decimal places)
     *         Thus you could use an alternative, '%hh:%mm:%ss:%ms', or '%hh:%mm:%ss' dependent on your usage.
     * @param mixed|boolean|integer $frames_per_second The number of frames per second to translate for. If left false
     *         the class automagically gets the fps from net_sourceforge_phpvideotoolkit_PhpVideoToolkit::getFileInfo(), but the input has to be set
     *         first for this to work properly.
     * @return string|integer Returns the timecode, but if $frames_per_second is not set and a frame rate lookup is required 
     *         but can't be reached then -1 will be returned.
     */
    public function formatSeconds($input_seconds, $return_format='%hh:%mm:%ss', $frames_per_second=false)
    {
        $timestamp         = mktime(0, 0, $input_seconds, 0, 0);
        $floored         = floor($input_seconds);
        $hours          = date('H', $timestamp);
        $mins              = date('i', $timestamp);
        $searches         = array();
        $replacements     = array();

        /* these ones are the simple replacements */
         /* replace the hours */
        $using_hours = strpos($return_format, '%hh') !== false;
        if ($using_hours) {
            array_push($searches, '%hh');
            array_push($replacements, $hours);
        }

        /* replace the minutes */
        $using_mins = strpos($return_format, '%mm') !== false;
        if ($using_mins) {
            array_push($searches, '%mm');

             /* check if hours are being used, if not and hours are required enable smart minutes */
            if (!$using_hours && $hours > 0) {
                $value = ($hours * 60) + $mins;
            } else {
                $value = $mins;
            }
            array_push($replacements, $value);
        }

         /* replace the seconds */
        if (strpos($return_format, '%ss') !== false) {

            /* check if hours are being used, if not and hours are required enable smart minutes */
            if (!$using_mins && !$using_hours && $hours > 0) {
                $mins = ($hours * 60) + $mins;
            }

            /* check if mins are being used, if not and hours are required enable smart minutes */
            if (!$using_mins && $mins > 0) {
                $value = ($mins * 60) + date('s', $timestamp);
            } else {
                $value = date('s', $timestamp);
            }
            array_push($searches, '%ss');
            array_push($replacements, $value);
        }

         /* replace the milliseconds */
        if (strpos($return_format, '%ms') !== false) {
            $milli = round($input_seconds - $floored, 3);
            $milli = substr($milli, 2);
            $milli = empty($milli) ? '0' : $milli;
            array_push($searches, '%ms');
            array_push($replacements, $milli);
        }

        /* replace the total seconds (rounded) */
        if (strpos($return_format, '%st') !== false) {
            array_push($searches, '%st');
            array_push($replacements, round($input_seconds));
        }

        /* replace the total seconds (floored) */
        if (strpos($return_format, '%sf') !== false) {
            array_push($searches, '%sf');
            array_push($replacements, floor($input_seconds));
        }

        /* replace the total seconds (ceiled) */
        if (strpos($return_format, '%sc') !== false) {
            array_push($searches, '%sc');
            array_push($replacements, ceil($input_seconds));
        }

         /* replace the total seconds */
        if (strpos($return_format, '%mt') !== false) {
            array_push($searches, '%mt');
            array_push($replacements, round($input_seconds, 3));
        }

        /* these are the more complicated as they depend on $frames_per_second / frames per second of the current input */
        $has_frames = strpos($return_format, '%fn') !== false;
        $has_total_frames = strpos($return_format, '%ft') !== false;
        if ($has_frames || $has_total_frames) {

            /* if the fps is false then we must automagically detect it from the input file */
            if ($frames_per_second === false) {
                $info = $this->getFileInfo();

                /* check the information has been received */
                if ($info === false || (!isset($info['video']) || !isset($info['video']['frame_rate']))) {
                    /* fps cannot be reached so return -1 */
                    return -1;
                }
                $frames_per_second = $info['video']['frame_rate'];
            }

            /* replace the frames */
            $excess_frames = false;
            if ($has_frames) {
                $excess_frames = ceil(($input_seconds - $floored) * $frames_per_second);
                array_push($searches, '%fn');
                array_push($replacements, $excess_frames);
            }
        
            /* replace the total frames (ie frame number) */
            if ($has_total_frames) {
                $round_frames = $floored * $frames_per_second;
                if (!$excess_frames) {
                    $excess_frames = ceil(($input_seconds - $floored) * $frames_per_second);
                }
                array_push($searches, '%ft');
                array_push($replacements, $round_frames + $excess_frames);
            }
        }
        return str_replace($searches, $replacements, $return_format);
    }

    /**
     * Translates a timecode to the number of seconds
     *
     * @access public
     * @param integer $input_seconds The number of seconds you want to calculate the timecode for.
     * @param integer $input_format The format of the timecode is being given in.
     *         default '%hh:%mm:%ss'
     *             - %hh (hours) representative of hours
     *             - %mm (minutes) representative of minutes
     *             - %ss (seconds) representative of seconds
     *             - %fn (frame number) representative of frames (of the current second, not total frames)
     *             - %ms (milliseconds) representative of milliseconds (of the current second, not total milliseconds) (rounded to 3 decimal places)
     *             - %ft (frames total) representative of total frames (ie frame number)
     *             - %st (seconds total) representative of total seconds (rounded).
     *             - %sf (seconds floored) representative of total seconds (floored).
     *             - %sc (seconds ceiled) representative of total seconds (ceiled).
     *             - %mt (milliseconds total) representative of total milliseconds. (rounded to 3 decimal places)
     *         Thus you could use an alternative, '%hh:%mm:%ss:%ms', or '%hh:%mm:%ss' dependent on your usage.
     * @param integer $return_format The format of the timecode to return. The default is
     *         default '%ts'
     *             - %hh (hours) representative of hours
     *             - %mm (minutes) representative of minutes
     *             - %ss (seconds) representative of seconds
     *             - %fn (frame number) representative of frames (of the current second, not total frames)
     *             - %ms (milliseconds) representative of milliseconds (of the current second, not total milliseconds) (rounded to 3 decimal places)
     *             - %ft (frames total) representative of total frames (ie frame number)
     *             - %st (seconds total) representative of total seconds (rounded).
     *             - %sf (seconds floored) representative of total seconds (floored).
     *             - %sc (seconds ceiled) representative of total seconds (ceiled).
     *             - %mt (milliseconds total) representative of total milliseconds. (rounded to 3 decimal places)
     *         Thus you could use an alternative, '%hh:%mm:%ss:%ms', or '%hh:%mm:%ss' dependent on your usage.
     * @param mixed|boolean|integer $frames_per_second The number of frames per second to translate for. If left false
     *         the class automagically gets the fps from net_sourceforge_phpvideotoolkit_PhpVideoToolkit::getFileInfo(), but the input has to be set
     *         first for this to work properly.
     * @return float Returns the value of the timecode in seconds.
     */
    public function formatTimecode($input_timecode, $input_format='%hh:%mm:%ss', $return_format='%ts', $frames_per_second=false)
    {

        /* first we must get the timecode into the current seconds */
        $input_quoted     = preg_quote($input_format);
        $placeholders     = array('%hh', '%mm', '%ss', '%fn', '%ms', '%ft', '%st', '%sf', '%sc', '%mt');
        $seconds     = 0;
        $input_regex     = str_replace($placeholders, '([0-9]+)', preg_quote($input_format));
        preg_match('/'.$input_regex.'/', $input_timecode, $matches);

         /* work out the sort order for the placeholders */
        $sort_table = array();
        foreach($placeholders as $key=>$placeholder) {
            if (($pos = strpos($input_format, $placeholder)) !== false) {
                $sort_table[$pos] = $placeholder;
            }
        }
        ksort($sort_table);

        /* check to see if frame related values are in the input */
        $has_frames = strpos($input_format, '%fn') !== false;
        $has_total_frames = strpos($input_format, '%ft') !== false;

        if ($has_frames || $has_total_frames) {

            /* if the fps is false then we must automagically detect it from the input file */
            if ($frames_per_second === false) {
                $info = $this->getFileInfo();

                /* check the information has been received */
                if ($info === false || (!isset($info['video']) || !isset($info['video']['frame_rate']))) {
                    /* fps cannot be reached so return -1 */
                    return -1;
                }
                $frames_per_second = $info['video']['frame_rate'];
            }
        }            

        /* increment the seconds with each placeholder value */
        $key = 1;
        foreach($sort_table as $placeholder) {
            if (!isset($matches[$key])) {
                break;
            }
            $value = $matches[$key];
            switch($placeholder) {

                 /* time related ones */
                case '%hh' : 
                    $seconds += $value * 3600;
                    break;
                case '%mm' : 
                    $seconds += $value * 60;
                    break;
                case '%ss' : 
                case '%sf' :
                case '%sc' :
                    $seconds += $value;
                    break;
                case '%ms' : 
                    $seconds += floatval('0.'.$value);
                    break;
                case '%st' : 
                case '%mt' :
                    $seconds = $value;
                    break 1;
                    break;

                 /* frame related ones */
                case '%fn' : 
                    $seconds += $value/$frames_per_second;
                    break;
                case '%ft' : 
                    $seconds = $value/$frames_per_second;
                    break 1;
                    break;
                key += 1;
            }
            return $this->formatSeconds($seconds, $return_format, $frames_per_second);
        }

    /**
     * Commits all the commands and executes the ffmpeg procedure. This will also attempt to validate any outputted files in order to provide
     * some level of stop and check system.
     *
     * @access public
     * @param $multi_pass_encode boolean Determines if multi (2) pass encoding should be used.
     * @param $log boolean Determines if a log file of the results should be generated.
     * @return mixed 
     *         - false                                         On error encountered.
     *         - net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RESULT_OK (bool true)                    If the file has successfully been processed and moved ok to the output address
     *         - net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RESULT_OK_BUT_UNWRITABLE (int -1)        If the file has successfully been processed but was not able to be moved correctly to the output address
     *                                                         If this is the case you will manually need to move the processed file from the temp directory. You can
     *                                                         get around this by settings the third argument from net_sourceforge_phpvideotoolkit_PhpVideoToolkit::setOutput(), $overwrite to true.
     *         - n (int)                                        A positive integer is only returned when outputting a series of frame grabs from a movie. It dictates
     *                                                         the total number of frames grabbed from the input video. You should also not however, that if a conflict exists
     *                                                         with one of the filenames then this return value will not be returned, but net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RESULT_OK_BUT_UNWRITABLE
     *                                                         will be returned instead.
     *     Because of the mixed return value you should always go a strict evaluation of the returned value. ie
     * 
     *     $result = $toolkit->excecute();
     *  if ($result === false)
     *  {
     *         // error
     *  }
     *  else if ($result === net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RESULT_OK_BUT_UNWRITABLE)
     *  {
     *         // ok but a manual move is required. The file to move can be it can be retrieved by $toolkit->getLastOutput();
     *  }
     *  else if ($result === net_sourceforge_phpvideotoolkit_PhpVideoToolkit::RESULT_OK)
     *  {
     *         // everything is ok.
     *  }
     */
    public function execute($multi_pass_encode=false, $log=false)
    {
        /* check for inut and output params */
        $has_placeholder = preg_match('/\%([0-9]+)index/', $this->_process_address) || (strpos($this->_process_address, '%index') === false && strpos($this->_process_address, '%timecode') === false);
        if ($this->_input_file === null && !$has_placeholder) {
            return $this->_raiseError('execute_input_404');
        }

        /* check to see if the output address has been set */
        if ($this->_process_address === null) {
            return $this->_raiseError('execute_output_not_set');
        }
            
        if (($this->_overwrite_mode == self::OVERWRITE_PRESERVE || $this->_overwrite_mode == self::OVERWRITE_FAIL) && is_file($this->_process_address)) {
            return $this->_raiseError('execute_overwrite_process');
        }
            
        /* carry out some overwrite checks if required */
        $overwrite = '';
        switch($this->_overwrite_mode) {
            case self::OVERWRITE_UNIQUE :
                /* insert a unique id into the output address (the process address already has one) */
                $unique = $this->unique();
                $last_index = strrpos($this->_output_address, DIRECTORY_SEPARATOR);
                $this->_output_address = substr($this->_output_address, 0, $last_index+1).$unique.'-'.substr($this->_output_address, $last_index+1);
                break;
                    
            case self::OVERWRITE_EXISTING :
                /* add an overwrite command to ffmpeg execution call */
                $overwrite = '-y ';
                break;
                    
            case self::OVERWRITE_PRESERVE :
                 /* do nothing as the preservation comes later */
                break;
                
            case self::OVERWRITE_FAIL :
            default :

                /* if the file should fail */
                if (!$has_placeholder && is_file($this->_output_address)) {
                    return $this->_raiseError('execute_overwrite_fail');
                }
                break;
        }
            
        $this->_timer_start = self::microtimeFloat();
            
        /* we have multiple inputs that require joining so convert them to a joinable format and join */
        if (is_array($this->_input_file)) {
            $this->_joinInput($log);
        }
            
        /* add the input file command to the mix */
        $this->addCommand('-i', $this->_input_file);
            
        /* if multi pass encoding is enabled add the commands and logfile */
        if ($multi_pass_encode) {
            $multi_pass_file = $this->_tmp_directory.$this->unique().'-multipass';
            $this->addCommand('-pass', 1);
            $this->addCommand('-passlogfile', $multi_pass_file);
        }

        /* combine all the output commands */
        $command_string = $this->_combineCommands();

        /**
         * prepare the command suitable for exec
         * the input and overwrite commands have specific places to be set so they have to be added outside of the combineCommands function
         */
        $exec_string = $this->_prepareCommand(PHPVIDEOTOOLKIT_FFMPEG_BINARY, $command_string, $overwrite.escapeshellcmd($this->_process_address));
        if ($log) {
            $this->_log_file = $this->_tmp_directory.$this->unique().'.info';
            array_push($this->_unlink_files, $this->_log_file);
            $exec_string = $exec_string.' &> '.$this->_log_file;
        }
            
        /* execute the command */
        exec($exec_string);
            
        /* track the processed command by adding it to the class */
        array_unshift($this->_processed, $exec_string);
            
        create the multiple pass encode
        if ($multi_pass_encode) {
            $pass2_exc_string = str_replace('-pass '.escapeshellarg(1), '-pass '.escapeshellarg(2), $exec_string);
            exec($pass2_exc_string);
            $this->_processed[0] = array($this->_processed[0], $pass2_exc_string);

             /* remove the multipass log file */
            unlink($multi_pass_file.'-0.log');
        }

        /* keep track of the time taken */
        $execution_time = self::microtimeFloat() - $this->_timer_start;
        array_unshift($this->_timers, $execution_time);
            
        /* add the exec string to the log file */
        if ($log) {
            $lines = $this->_processed[0];
            if (!is_array($lines)) {
                $lines = array($lines);
            }
            array_unshift($lines, $this->_getMessage('ffmpeg_log_separator'), $this->_getMessage('ffmpeg_log_ffmpeg_command'), $this->_getMessage('ffmpeg_log_separator'));
            array_unshift($lines, $this->_getMessage('ffmpeg_log_separator'), $this->_getMessage('ffmpeg_log_ffmpeg_gunk'), $this->_getMessage('ffmpeg_log_separator'));
            $this->_addToLog($lines, 'r+');
        }

        /* must validate a series of outputed items */
        /* detect if the output address is a sequence output */
        if (preg_match('/\%([0-9]+)d/', $this->_process_address, $d_matches) || strpos($this->_process_address, '%d') !== false) {

            /* get the path details */
            $process_info     = pathinfo($this->_process_address);
            $output_info     = pathinfo($this->_output_address);
            $pad_amount     = intval($d_matches[1]);
                
             /* get the %index padd amounts */
            $has_preg_index = preg_match('/\%([0-9]+)index/', $output_info['basename'], $index_matches);
            $output_index_pad_amount = isset($index_matches[1]) ? intval($index_matches[1], 1) : 0;
                
            /* init the iteration values */
            $num             = 1;
            $files             = array();
            $produced         = array();
            $error            = false;
            $name_conflict    = false;
            $file_exists    = false;
                
             /* get the first files name */
            $filename         = $process_info['dirname'].DIRECTORY_SEPARATOR.str_replace($d_matches[0], str_pad($num, $pad_amount, '0', STR_PAD_LEFT), $process_info['basename']);
            $use_timecode    = strpos($output_info['basename'], '%timecode') !== false;
            $use_index        = $has_preg_index || strpos($output_info['basename'], '%index') !== false;
                
             /* start the timecode pattern replacement values */
            if ($use_timecode) {
                $secs_start = $this->formatTimecode($this->_image_output_timecode_start, '%hh:%mm:%ss.%ms', '%mt', $this->_image_output_timecode_fps);
                $fps_inc = 1/$this->_image_output_timecode_fps;
                $fps_current_sec = 0;
                $fps_current_frame = 0;
            }
                
            /* loop checking for file existence */
            while(@is_file($filename)) {

                /* check for empty file */
                $size = filesize($filename);
                if ($size == 0) {
                    $error = true;
                }
                array_push($produced, $filename);
                     
                /* create the substitution arrays */
                $searches         = array();
                $replacements     = array();
                if ($use_index) {
                    array_push($searches, isset($index_matches[0]) ? $index_matches[0] : '%index');
                    array_push($replacements, str_pad($num, $output_index_pad_amount, '0', STR_PAD_LEFT));
                }

                /* check if timecode is in the output name, no need to use it if not */
                if ($use_timecode) {
                    $fps_current_sec     += $fps_inc;
                    $fps_current_frame     += 1;
                    if ($fps_current_sec >= 1) {
                        $fps_current_sec      = $fps_inc;
                        $secs_start         += 1;
                        $fps_current_frame      = 1;
                    }
                    $timecode = $this->formatSeconds($secs_start, $this->image_output_timecode_format, $this->_image_output_timecode_fps);
                    $timecode         = str_replace(array(':', '.'), $this->timecode_seperator_output, $timecode);
                     
                    /* add to the substitution array */
                    array_push($searches, '%timecode');
                    array_push($replacements, $timecode);
                }

                /* check if the file exists already and if it does check that it can be overriden */
                $old_filename = $filename;
                $new_file = str_replace($searches, $replacements, $output_info['basename']);
                $new_filename = $output_info['dirname'].DIRECTORY_SEPARATOR.$new_file;

                if (!is_file($new_filename) || $this->_overwrite_mode == self::OVERWRITE_EXISTING) {
                    rename($filename, $new_filename);
                    $filename = $new_filename;
                } else if ($this->_overwrite_mode == self::OVERWRITE_PRESERVE) {
                        
                    /* the file exists and is not allowed to be overriden so just rename in the temp directory using the timecode */
                    $new_filename = $process_info['dirname'].DIRECTORY_SEPARATOR.'tbm-'.$this->unique().'-'.$new_file;
                    rename($filename, $new_filename);
                    $filename = $new_filename;
                    
                    /* add the error to the log file */
                    if ($log) {
                        $this->_logResult('execute_image_file_exists', array('file'=>$new_filename));
                    }

                     /* flag the conflict */
                    $file_exists = true;
                } else {

                    /* add the error to the log file */
                    if ($log) {
                        $this->_logResult('execute_overwrite_fail');
                    }
                
                    /* tidy up the produced files */
                    array_merge($this->_unlink_files, $produced);
                    return $this->_raiseError('execute_overwrite_fail');
                }

                /* process the name change if the %d is to be replaced with the timecode */
                $num += 1;
                $files[$filename] = $size > 0 ? basename($filename) : false;

                 /* get the next incremented filename to check for existance */
                $filename = $process_info['dirname'].DIRECTORY_SEPARATOR.str_replace($d_matches[0], str_pad($num, $pad_amount, '0', STR_PAD_LEFT), $process_info['basename']);
            }

            /* de-increment the last num as it wasn't found */
            $num -= 1;

            /* if the file was detected but were empty then display a different error */
            if ($error === true) {

                /* add the error to the log file */
                if ($log) {
                    $this->_logResult('execute_partial_error', array('input'=>$this->_input_file));
                }
                return $this->_raiseError('execute_partial_error', array('input'=>$this->_input_file));
            }

            /* post process any files */
            $post_process_result = $this->_postProcess($log, $files);

            if (is_array($post_process_result)) {

                /* post process has occurred and everything is fine */
                $num = count($files);
            } else if ($post_process_result !== false) {

                /* the file has encountered an error in the post processing of the files */
                return $post_process_result;
            }

            /* if the result is false then no post process has taken place */
            $this->_process_file_count = $num;

            /* no files were generated in this sequence */
            if ($num == 0) {

                /* add the error to the log file */
                if ($log) {
                    $this->_logResult('execute_image_error', array('input'=>$this->_input_file));
                }
                return $this->_raiseError('execute_image_error', array('input'=>$this->_input_file));
            }
                
            /* add the files the the class a record of what has been generated */
            array_unshift($this->_files, $files);    
            array_push($lines, $this->_getMessage('ffmpeg_log_separator'), $this->_getMessage('ffmpeg_log_ffmpeg_output'), $this->_getMessage('ffmpeg_log_separator'), implode("\n", $files));
            $this->_addToLog($lines, 'r+');
                
            return $file_exists ? self::RESULT_OK_BUT_UNWRITABLE : self::RESULT_OK;

        } else {

            /* check that it is a file */
            if (!is_file($this->_process_address)) {
                if ($log) {
                    $this->_logResult('execute_output_404', array('input'=>$this->_input_file));
                }
                return $this->_raiseError('execute_output_404', array('input'=>$this->_input_file));
            }
                
            /* the file does exist but is it empty? */
            if (filesize($this->_process_address) == 0) {
                if ($log) {
                    $this->_logResult('execute_output_empty', array('input'=>$this->_input_file));
                }
                return $this->_raiseError('execute_output_empty', array('input'=>$this->_input_file));
            }

            /* the file is ok so move to output address */
            if (!is_file($this->_output_address) || $this->_overwrite_mode == self::OVERWRITE_EXISTING) {

                $post_process_result = $this->_postProcess($log, array($this->_process_address));

                if (is_array($post_process_result) || $post_process_result === true) {
                     /* post process has occurred and everything is fine */
                } else if ($post_process_result !== false) {
                    return $post_process_result;
                }

                /* if the result is false then no post process has taken place */
                /* rename the file to the final destination and check it went ok */
                if (rename($this->_process_address, $this->_output_address)) {
                    array_push($lines, $this->_getMessage('ffmpeg_log_separator'), $this->_getMessage('ffmpeg_log_ffmpeg_output'), $this->_getMessage('ffmpeg_log_separator'), $this->_output_address);
                    $this->_addToLog($lines, 'r+');
                        
                    /* the file has been renamed ok */
                    if ($log) {
                        $this->_logResult('execute_result_ok', array('output'=>$this->_output_address));
                    }
                    $this->_process_file_count = 1;
                    
                    /* add the file the the class a record of what has been generated */
                    array_unshift($this->_files, array($this->_output_address));
                    return self::RESULT_OK;
                } else {
                    if ($log) {
                        $this->_logResult('execute_result_ok_but_unwritable', array('process'=>$this->_process_address, 'output'=>$this->_output_address));
                    }

                    /* add the file the the class a record of what has been generated */
                    array_unshift($this->_files, array($this->_process_address));
                    array_push($lines, $this->_getMessage('ffmpeg_log_separator'), $this->_getMessage('ffmpeg_log_ffmpeg_output'), $this->_getMessage('ffmpeg_log_separator'), $this->_process_address);
                    $this->_addToLog($lines, 'r+');
                    return self::RESULT_OK_BUT_UNWRITABLE;
                }
            } else if ($this->_overwrite_mode == self::OVERWRITE_PRESERVE) {

                if ($log) {
                    $this->_logResult('execute_result_ok_but_unwritable', array('process'=>$this->_process_address, 'output'=>$this->_output_address));
                }

                /* add the file the the class a record of what has been generated */
                array_unshift($this->_files, array($this->_process_address));
                return self::RESULT_OK_BUT_UNWRITABLE;
            } else {

                if ($log) {
                    $this->_logResult('execute_overwrite_fail');
                }

                 /* tidy up the produced files */
                array_push($this->_unlink_files, $this->_process_address);
                return $this->_raiseError('execute_overwrite_fail');
            }
        }

        return null;

    }
        
    /**
     * This function registers a post process after the internal handling of the ffmpeg output has been cleaned and checked.
     * Each function that is set will be called in the order it is set unless an index is specified. All callbacks will be 
     * supplied with one argument with is an array of the outputted files. 
     * 
     * NOTE1: If a post process function is being applied to an outputted video or audio then the process will be applied 
     * before it has been moved to it's final destination, however if the output is an image sequence the post process 
     * function will be called after the images have been moved to their final destinations.
     * 
     * NOTE2: Also it is important to return a boolean 'true' if the post process has been carried out ok. If the process is not
     * a true value then the value will be treated/returned as an error and if applicable logged.
     * 
         * @access public
     * @param string $function The name of a function
     * @param object|boolean $class The name of the callback class. If left as false the callback will be treated as a standalone function.
     * @param integer|boolean $index The index of the callback array to put the callback into. If left as false it will be pushed to the end of the array.
     */
    public function registerPostProcess($function, $class=false, $index=false)
    {
        /* create the callback */
        $callback = $class === false ? $function : array(&$class, $function);
             
        /* add it to the post process array */
        if ($index === false) {
            array_push($this->_post_processes, $callback);
        } else {
            $this->_post_processes[$index] = $callback;
        }
    }
        
    /**
     * Carries out the post processing of the files.
     * 
     * @access private
     * @param boolean $log Determines if logging of errors should be carried out.
     * @param array $files The array of files that have just been processed.
     * @return mixed
     */
    private function _postProcess($log, $files)
    {
        if (count($this->_post_processes)) {

             /* loop through the post processes */
            foreach($this->_post_processes as $key=>$process) {

                /* call the process */
                $return_value = call_user_func_array($process, array($files));
                 
                /* if the return value is not strictly equal to true the result will be treated as an error and exit the process loop */
                if (!is_array($return_value) && $return_value !== true) {
                    if ($log) {
                        $this->_logResult($return_value);
                    }
                    return $this->_raiseError($return_value);
                }
            }
            return $return_value;
        }
        return false;
    }
        
    /**
     * Returns the number of files outputted in this run. It will be reset when you call net_sourceforge_phpvideotoolkit_PhpVideoToolkit::reset();
     * 
     * @access public
     * @return integer
     */
    public function getFileOutputCount()
    {
        return $this->_process_file_count;
    }
        
    /**
     * Adds lines to the current log file.
     * 
     * @access private
     * @param $message
     * @param $replacements
     */
    private function _logResult($message, $replacements=false)
    {
        $this->_addToLog(array($this->_getMessage('ffmpeg_log_separator'), $this->_getMessage('ffmpeg_log_ffmpeg_result'), $this->_getMessage('ffmpeg_log_separator'), $this->_getMessage($message, $replacements)));
    }
        
    /**
     * Adds lines to the current log file.
     * 
     * @access private
     * @param $lines array An array of lines to add to the log file.
     */
    private function _addToLog($lines, $where='a')
    {
        $handle = fopen($this->_log_file, $where);
        if (is_array($lines)) {
            $data = implode("\n", $lines)."\n";
        } else {
            $data = $lines."\n";
        }
        fwrite($handle, $data);
        fclose($handle);
    }
        
    /**
     * Moves the current log file to another file.
     * 
     * @access public
     * @param $destination string The absolute path of the new filename for the log.
     * @return boolean Returns the result of the log file rename.
     */
    public function moveLog($destination)
    {
        $result = false;
        if ($this->_log_file !== null && is_file($this->_log_file)) {
            $result = rename($this->_log_file, $destination);
            $this->_log_file = $destination;
        }
        return $result;
    }

    /**
     * Reads the current log file
     * 
     * @access public
     * @return string|boolean Returns the current log file content. Returns false on failure.
     */
    public function readLog()
    {
        if ($this->_log_file !== null && is_file($this->_log_file)) {
            $handle = fopen($this->_log_file, 'r');
            $contents = fread($handle, filesize($this->_log_file));
            fclose($handle);
            return $contents;
        }
        return false;
    }

    /**
     * Returns the last outputted file that was processed by ffmpeg from this class.
     *
     * @access public
     * @return mixed array|string Will return an array if the output was a sequence, or string if it was a single file output
     */
    public function getLastOutput()
    {
        return $this->_files[0];
    }

    /**
     * Returns all the outputted files that were processed by ffmpeg from this class.
     *
     * @access public
     * @return array
     */
    public function getOutput()
    {
        return $this->_files;
    }

    /**
     * Returns the amount of time taken of the last file to be processed by ffmpeg.
     *
     * @access public
     * @return mixed integer Will return the time taken in seconds.
     */
    public function getLastProcessTime()
    {
        return $this->_timers[0];
    }

    /**
     * Returns the amount of time taken of all the files to be processed by ffmpeg.
     *
     * @access public
     * @return array
     */
    public function getProcessTime()
    {
        return $this->_timers;
    }

    /**
     * Returns the last encountered error message.
     *
     * @access public
     * @return string
     */
    public function getLastError()
    {
        return $this->_errors[0];
    }

    /**
     * Returns all the encountered errors as an array of strings
     *
     * @access public
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Returns the last command that ffmpeg was given.
     * (Note; if setFormatToFLV was used in the last command then an array is returned as a command was also sent to FLVTool2)
     *
     * @access public
     * @return mixed array|string
     */
    public function getLastCommand()
    {
        return $this->_processed[0];
    }

    /**
     * Returns all the commands sent to ffmpeg from this class
     *
     * @access public
     * @return unknown
     */
    public function getCommands()
    {
        return $this->_processed;
    }

    /**
     * Raises an error
     *
     * @access private
     * @param string $message
     * @param array $replacements a list of replacements in search=>replacement format
     * @return boolean Only returns false if $toolkit->on_error_die is set to false
     */
    private function _raiseError($message, $replacements=false)
    {
        $msg = 'FFMPEG ERROR: '.$this->_getMessage($message, $replacements);

        if ($this->on_error_die === true) {
            die($msg);
        }

        array_unshift($this->_errors, $msg);
        return false;
    }

    /**
     * Gets a message.
     *
     * @access private
     * @param string $message
     * @param array $replacements a list of replacements in search=>replacement format
     * @return boolean Only returns false if $toolkit->on_error_die is set to false
     */
    private function _getMessage($message, $replacements=false)
    {
        $message = isset($this->_messages[$message]) ? $this->_messages[$message] : 'Unknown!!!';
        if ($replacements) {
            $searches = $replaces = array();
            foreach($replacements as $search=>$replace) {
                array_push($searches, '#'.$search);
                array_push($replaces, $replace);
            }
            $message = str_replace($searches, $replaces, $message);
        }
        return $message;
    }

    /**
     * Adds a command to be bundled into the ffmpeg command call.
     * (SPECIAL NOTE; None of the arguments are checked or sanitized by this function. BE CAREFUL if manually using this. The commands and arguments are escaped
     * however it is still best to check and sanitize any params given to this function)
     *
     * @access public
     * @param string $command
     * @param mixed $argument
     * @return boolean
     */
    public function addCommand($command, $argument='')
    {
        $this->_commands[$command] = escapeshellarg($argument);
        return true;
    }

    /**
     * Determines if the the command exits.
     *
     * @access public
     * @param string $command
     * @return mixed boolean if failure or value if exists.
     */
    public function hasCommand($command)
    {
        return isset($this->_commands[$command]) ? $this->_commands[$command] : false;
    }

    /**
     * Combines the commands stored into a string
     *
     * @access private
     * @return string
     */
    private function _combineCommands()
    {
        $before_input     = array();
        $after_input     = array();
        $input             = null;
        foreach ($this->_commands as $command=>$argument) {
            $command_string = trim($command.($argument ? ' '.$argument : ''));

            /* check for specific none combinable commands as they have specific places they have to go in the string */
            switch($command) {
                case '-i' :
                    $input = $command_string;
                    break;
                case '-inputr' :
                    $command_string = trim('-r'.($argument ? ' '.$argument : ''));;
                default :
                    if (in_array($command, $this->_cmds_before_input)) {
                        array_push($before_input, $command_string);
                    } else {
                        array_push($after_input, $command_string);
                    }
            }
        }
            
        $before_input = count($before_input) ? implode(' ', $before_input).' ' : '';
        $after_input_string = ' ';
        if (count($after_input)) {
            $input .= ' ';
            $after_input_string  = implode(' ', $after_input).' ';
        }
            
        return $before_input.$input.$after_input_string;
    }

    /**
     * Prepares the command for execution
     *
     * @access private
     * @param string $path Path to the binary
     * @param string $command Command string to execute
     * @param string $args Any additional arguments
     * @return string
     */
    private function _prepareCommand($path, $command, $args='')
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN' || !preg_match('/\s/', $path)) {
                return $path.' '.$command.' '.$args;
            }
            return 'start /D "'.$path.'" /B '.$command.' '.$args;
    }

    /**
     * Generates a unique id. Primarily used in jpeg to movie production
     *
     * @access public
     * @param string $prefix
     * @return string
     */
    public function unique($prefix='')
    {
        return uniqid($prefix.time().'-');
    }

    /**
     * Destructs ffmpeg and removes any temp files/dirs
     * @access private
     */
    function __destruct()
    {
        /* loop through the temp files to remove first as they have to be removed before the dir can be removed */
        if (!empty($this->_unlink_files)) {
            foreach ($this->_unlink_files as $key=>$file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            $this->_unlink_files = array();
        }

        /* loop through the dirs to remove */
        if (!empty($this->_unlink_dirs)) {
            foreach ($this->_unlink_dirs as $key=>$dir) {
                if (is_dir($dir)) {
                    @rmdir($dir);
                }
            }
            $this->_unlink_dirs = array();
        }
    }
}

